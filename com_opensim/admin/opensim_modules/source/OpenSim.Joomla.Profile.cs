/*
 * Copyright (c) FoTo50
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the OpenSimulator Project nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE DEVELOPERS ``AS IS'' AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE CONTRIBUTORS BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

using System;
using System.Collections;
using System.Collections.Generic;
using System.Globalization;
using System.Net;
using System.Net.Sockets;
using System.Reflection;
using System.Xml;
using OpenMetaverse;
using log4net;
using Nini.Config;
using Nwc.XmlRpc;
using OpenSim.Framework;
using OpenSim.Region.Framework.Interfaces;
using OpenSim.Region.Framework.Scenes;
using OpenSim.Services.Interfaces;


namespace jOpenSim.Profile.jOpenProfile
{
    public class OpenProfileModule : IRegionModule
    {
        //
        // Log module
        //
        private static readonly ILog m_log = LogManager.GetLogger(MethodBase.GetCurrentMethod().DeclaringType);

        //
        // Module vars
        //
        private IConfigSource m_gConfig;
        private List<Scene> m_Scenes = new List<Scene>();
        private string m_ProfileServer = "";
        private string m_ProfileModul = "";
        private bool m_Enabled = false;

        public void Initialise(Scene scene, IConfigSource config)
        {
            m_log.DebugFormat("[jOpenSim.Profile]: Initialise");
            IConfig profileConfig = config.Configs["Profile"];

            if (m_Scenes.Count == 0) // First time
            {
                if (profileConfig == null)
                {
                	m_log.DebugFormat("[jOpenSim.Profile]: jOpenSimProfile disabled! Reason: [Profile] section not found");
                    m_Enabled = false;
                    return;
                }
                m_ProfileServer = profileConfig.GetString("ProfileURL", "");
                m_ProfileModul  = profileConfig.GetString("Module", "");

                if (m_ProfileModul != "jOpenSimProfile")
                {
                	m_log.DebugFormat("[jOpenSim.Profile]: jOpenSimProfile disabled! Reason: Module Name in [Profile section] invalid or not found");
                    m_Enabled = false;
                    return;
                }

                if (m_ProfileServer == "")
                {
                	m_log.DebugFormat("[jOpenSim.Profile]: jOpenSimProfile disabled (no ProfileURL found)");
                    m_Enabled = false;
                    return;
                }
                else
                {
                    m_log.Info("[jOpenSim.Profile] Profile module is activated, communicating with " + m_ProfileServer);
                    m_Enabled = true;
                }
            }

//            if (!m_Enabled)
//                return;

            if (!m_Scenes.Contains(scene))
                m_Scenes.Add(scene);

            m_gConfig = config;

            // Hook up events
            scene.EventManager.OnNewClient += OnNewClient;
        }

        public void PostInitialise()
        {
            if (!m_Enabled)
                return;
        }

        public void Close()
        {
        }

        public string Name
        {
            get { return "jOpenSimProfileModule (compatible with 0.7.3-Dev)"; }
        }

        public bool IsSharedModule
        {
            get { return true; }
        }

        ScenePresence FindPresence(UUID clientID)
        {
            ScenePresence p;

            foreach (Scene s in m_Scenes)
            {
                p = s.GetScenePresence(clientID);
                if (p != null && !p.IsChildAgent)
                    return p;
            }
            return null;
        }

        /// New Client Event Handler
        private void OnNewClient(IClientAPI client)
        {
            // Subscribe to messages

            // Classifieds
            client.AddGenericPacketHandler("avatarclassifiedsrequest", HandleAvatarClassifiedsRequest);
            client.OnClassifiedInfoRequest += ClassifiedInfoRequest;
            client.OnClassifiedInfoUpdate += ClassifiedInfoUpdate;
            client.OnClassifiedDelete += ClassifiedDelete;

            // Picks
            client.AddGenericPacketHandler("avatarpicksrequest", HandleAvatarPicksRequest);
            client.AddGenericPacketHandler("pickinforequest", HandlePickInfoRequest);
            client.OnPickInfoUpdate += PickInfoUpdate;
            client.OnPickDelete += PickDelete;

            // Notes
            client.AddGenericPacketHandler("avatarnotesrequest", HandleAvatarNotesRequest);
            client.OnAvatarNotesUpdate += AvatarNotesUpdate;

            //Profile
            client.OnRequestAvatarProperties += RequestAvatarProperties;
            client.OnUpdateAvatarProperties += UpdateAvatarProperties;
            client.OnAvatarInterestUpdate += AvatarInterestsUpdate;
            client.OnUserInfoRequest += UserPreferencesRequest;
            client.OnUpdateUserInfo += UpdateUserPreferences;
        }

        //
        // Make external XMLRPC request
        //
        private Hashtable GenericXMLRPCRequest(Hashtable ReqParams, string method)
        {
//          m_log.ErrorFormat("[jOpenSim.Profile] send method "+method+" to "+m_ProfileServer);
            
            ArrayList SendParams = new ArrayList();
            SendParams.Add(ReqParams);

            // Send Request
            XmlRpcResponse Resp;
            try
            {
                XmlRpcRequest Req = new XmlRpcRequest(method, SendParams);
                Resp = Req.Send(m_ProfileServer, 30000);
            }
            catch (WebException ex)
            {
                m_log.ErrorFormat("[PROFILE]: Unable to connect to Profile " +
                        "Server {0}.  Exception {1}", m_ProfileServer, ex);

                Hashtable ErrorHash = new Hashtable();
                ErrorHash["success"] = false;
                ErrorHash["errorMessage"] = "Unable to fetch profile data at this time. ";
                ErrorHash["errorURI"] = "";

                return ErrorHash;
            }
            catch (SocketException ex)
            {
                m_log.ErrorFormat(
                        "[PROFILE]: Unable to connect to Profile Server {0}. Method {1}, params {2}. " +
                        "Exception {3}", m_ProfileServer, method, ReqParams, ex);

                Hashtable ErrorHash = new Hashtable();
                ErrorHash["success"] = false;
                ErrorHash["errorMessage"] = "Unable to fetch profile data at this time. ";
                ErrorHash["errorURI"] = "";

                return ErrorHash;
            }
            catch (XmlException ex)
            {
                m_log.ErrorFormat(
                        "[PROFILE]: Unable to connect to Profile Server {0}. Method {1}, params {2}. " +
                        "Exception {3}", m_ProfileServer, method, ReqParams.ToString(), ex);
                Hashtable ErrorHash = new Hashtable();
                ErrorHash["success"] = false;
                ErrorHash["errorMessage"] = "Unable to fetch profile data at this time. ";
                ErrorHash["errorURI"] = "";

                return ErrorHash;
            }
            if (Resp.IsFault)
            {
                Hashtable ErrorHash = new Hashtable();
                ErrorHash["success"] = false;
                ErrorHash["errorMessage"] = "Unable to fetch profile data at this time. ";
                ErrorHash["errorURI"] = "";
                return ErrorHash;
            }
            Hashtable RespData = (Hashtable)Resp.Value;

            return RespData;
        }

        // Classifieds Handler

        public void HandleAvatarClassifiedsRequest(Object sender, string method, List<String> args)
        {
            if (!(sender is IClientAPI))
                return;

            IClientAPI remoteClient = (IClientAPI)sender;

            Hashtable ReqHash = new Hashtable();
            ReqHash["uuid"] = args[0];

            Hashtable result = GenericXMLRPCRequest(ReqHash,
                    method);

            if (!Convert.ToBoolean(result["success"]))
            {
                remoteClient.SendAgentAlertMessage(
                        result["errorMessage"].ToString(), false);
                return;
            }

            ArrayList dataArray = (ArrayList)result["data"];

            Dictionary<UUID, string> classifieds = new Dictionary<UUID, string>();

            foreach (Object o in dataArray)
            {
                Hashtable d = (Hashtable)o;

                classifieds[new UUID(d["classifiedid"].ToString())] = d["name"].ToString();
            }

            remoteClient.SendAvatarClassifiedReply(new UUID(args[0]), classifieds);
        }

        // Request Classifieds
        public void ClassifiedInfoRequest(UUID classifiedID, IClientAPI client)
        {
            Hashtable ReqHash = new Hashtable();

            ReqHash["avatar_id"]      = client.AgentId.ToString();
            ReqHash["classified_id"] = classifiedID.ToString();
            
            Hashtable result = GenericXMLRPCRequest(ReqHash, "classifiedinforequest");
            if (!Convert.ToBoolean(result["success"]))
            {
                client.SendAgentAlertMessage(result["errorMessage"].ToString(), false);
                return;
            }

            ArrayList dataArray = (ArrayList)result["data"];

            if (dataArray!=null && dataArray[0]!=null)
            {
                Hashtable d = (Hashtable)dataArray[0];

                Vector3 globalPos = new Vector3();
                Vector3.TryParse(d["posglobal"].ToString(), out globalPos);

                if (d["description"]==null) d["description"] = String.Empty;

                string name = d["name"].ToString();
                string desc = d["description"].ToString();

                client.SendClassifiedInfoReply(    new UUID(d["classifieduuid"].ToString()),
                                                 new UUID(d["creatoruuid"].ToString()),
                                                Convert.ToUInt32(d["creationdate"]),
                                                Convert.ToUInt32(d["expirationdate"]),
                                                Convert.ToUInt32(d["category"]),
                                                name,
                                                desc,
                                                new UUID(d["parceluuid"].ToString()),
                                                Convert.ToUInt32(d["parentestate"]),
                                                new UUID(d["snapshotuuid"].ToString()),
                                                 d["simname"].ToString(),
                                                globalPos,
                                                d["parcelname"].ToString(),
                                                Convert.ToByte(d["classifiedflags"]),
                                                 Convert.ToInt32(d["priceforlisting"]));
            }
        }

        // Classifieds Update

        public void ClassifiedInfoUpdate(UUID queryclassifiedID, uint queryCategory, string queryName, string queryDescription, UUID queryParcelID,
                                        uint queryParentEstate, UUID querySnapshotID, Vector3 queryGlobalPos, byte queryclassifiedFlags,
                                        int queryclassifiedPrice, IClientAPI remoteClient)
        {
            Hashtable ReqHash = new Hashtable();

            ReqHash["creatorUUID"] = remoteClient.AgentId.ToString();
            ReqHash["classifiedUUID"] = queryclassifiedID.ToString();
            ReqHash["category"] = queryCategory.ToString();
            ReqHash["name"] = queryName;
            ReqHash["description"] = queryDescription;
            ReqHash["parentestate"] = queryParentEstate.ToString();
            ReqHash["snapshotUUID"] = querySnapshotID.ToString();
            ReqHash["sim_name"] = remoteClient.Scene.RegionInfo.RegionName;
            ReqHash["globalpos"] = queryGlobalPos.ToString();
            ReqHash["classifiedFlags"] = queryclassifiedFlags.ToString();
            ReqHash["classifiedPrice"] = queryclassifiedPrice.ToString();

            ScenePresence p = FindPresence(remoteClient.AgentId);

            Vector3 avaPos = p.AbsolutePosition;

            // Getting the parceluuid for this parcel

            ReqHash["parcel_uuid"] = p.currentParcelUUID.ToString();

            // Getting the global position for the Avatar

            Vector3 posGlobal = new Vector3(remoteClient.Scene.RegionInfo.RegionLocX * Constants.RegionSize + avaPos.X,
                                            remoteClient.Scene.RegionInfo.RegionLocY * Constants.RegionSize + avaPos.Y,
                                            avaPos.Z);

            ReqHash["pos_global"] = posGlobal.ToString();

            Hashtable result = GenericXMLRPCRequest(ReqHash,
                    "classified_update");

            if (!Convert.ToBoolean(result["success"]))
            {
                remoteClient.SendAgentAlertMessage(
                        result["errorMessage"].ToString(), false);
            }
        }

        // Classifieds Delete

        public void ClassifiedDelete (UUID queryClassifiedID, IClientAPI remoteClient)
        {
            Hashtable ReqHash = new Hashtable();

            ReqHash["classifiedID"] = queryClassifiedID.ToString();

            Hashtable result = GenericXMLRPCRequest(ReqHash,
                    "classified_delete");

            if (!Convert.ToBoolean(result["success"]))
            {
                remoteClient.SendAgentAlertMessage(
                        result["errorMessage"].ToString(), false);
            }
        }

        // Picks Handler

        public void HandleAvatarPicksRequest(Object sender, string method, List<String> args)
        {
            if (!(sender is IClientAPI))
                return;

            IClientAPI remoteClient = (IClientAPI)sender;

            Hashtable ReqHash = new Hashtable();
            ReqHash["uuid"] = args[0];

            Hashtable result = GenericXMLRPCRequest(ReqHash,
                    method);

            if (!Convert.ToBoolean(result["success"]))
            {
                remoteClient.SendAgentAlertMessage(
                        result["errorMessage"].ToString(), false);
                return;
            }

            ArrayList dataArray = (ArrayList)result["data"];

            Dictionary<UUID, string> picks = new Dictionary<UUID, string>();

            if (dataArray != null)
            {
                foreach (Object o in dataArray)
                {
                    Hashtable d = (Hashtable)o;

                    picks[new UUID(d["pickid"].ToString())] = d["name"].ToString();
                }
            }

            remoteClient.SendAvatarPicksReply(new UUID(args[0]), picks);
        }

        // Picks Request

        public void HandlePickInfoRequest(Object sender, string method, List<String> args)
        {
            if (!(sender is IClientAPI))
                return;

            IClientAPI remoteClient = (IClientAPI)sender;

            Hashtable ReqHash = new Hashtable();

            ReqHash["avatar_id"] = args[0];
            ReqHash["pick_id"] = args[1];

            Hashtable result = GenericXMLRPCRequest(ReqHash,
                    method);

            if (!Convert.ToBoolean(result["success"]))
            {
                remoteClient.SendAgentAlertMessage(
                        result["errorMessage"].ToString(), false);
                return;
            }

            ArrayList dataArray = (ArrayList)result["data"];

            Hashtable d = (Hashtable)dataArray[0];

            Vector3 globalPos = new Vector3();
            Vector3.TryParse(d["posglobal"].ToString(), out globalPos);

            if (d["description"] == null)
                d["description"] = String.Empty;

            remoteClient.SendPickInfoReply(
                    new UUID(d["pickuuid"].ToString()),
                    new UUID(d["creatoruuid"].ToString()),
                    Convert.ToBoolean(d["toppick"]),
                    new UUID(d["parceluuid"].ToString()),
                    d["name"].ToString(),
                    d["description"].ToString(),
                    new UUID(d["snapshotuuid"].ToString()),
                    d["user"].ToString(),
                    d["originalname"].ToString(),
                    d["simname"].ToString(),
                    globalPos,
                    Convert.ToInt32(d["sortorder"]),
                    Convert.ToBoolean(d["enabled"]));
        }

        // Picks Update

        public void PickInfoUpdate(IClientAPI remoteClient, UUID pickID, UUID creatorID, bool topPick, string name, string desc, UUID snapshotID, int sortOrder, bool enabled)
        {
            Hashtable ReqHash = new Hashtable();

            ReqHash["agent_id"] = remoteClient.AgentId.ToString();
            ReqHash["pick_id"] = pickID.ToString();
            ReqHash["creator_id"] = creatorID.ToString();
            ReqHash["top_pick"] = topPick.ToString();
            ReqHash["name"] = name;
            ReqHash["desc"] = desc;
            ReqHash["snapshot_id"] = snapshotID.ToString();
            ReqHash["sort_order"] = sortOrder.ToString();
            ReqHash["enabled"] = enabled.ToString();
            ReqHash["sim_name"] = remoteClient.Scene.RegionInfo.RegionName;

            ScenePresence p = FindPresence(remoteClient.AgentId);

            Vector3 avaPos = p.AbsolutePosition;

            // Getting the parceluuid for this parcel

            ReqHash["parcel_uuid"] = p.currentParcelUUID.ToString();

            // Getting the global position for the Avatar

            Vector3 posGlobal = new Vector3(remoteClient.Scene.RegionInfo.RegionLocX*Constants.RegionSize + avaPos.X,
                                            remoteClient.Scene.RegionInfo.RegionLocY*Constants.RegionSize + avaPos.Y,
                                            avaPos.Z);

            ReqHash["pos_global"] = posGlobal.ToString();

            // Getting the owner of the parcel
            ReqHash["user"] = "";   //FIXME: Get avatar/group who owns parcel

            // Do the request
            Hashtable result = GenericXMLRPCRequest(ReqHash,
                    "picks_update");

            if (!Convert.ToBoolean(result["success"]))
            {
                remoteClient.SendAgentAlertMessage(
                        result["errorMessage"].ToString(), false);
            }
        }

        // Picks Delete

        public void PickDelete(IClientAPI remoteClient, UUID queryPickID)
        {
            Hashtable ReqHash = new Hashtable();

            ReqHash["pick_id"] = queryPickID.ToString();

            Hashtable result = GenericXMLRPCRequest(ReqHash,
                    "picks_delete");

            if (!Convert.ToBoolean(result["success"]))
            {
                remoteClient.SendAgentAlertMessage(
                        result["errorMessage"].ToString(), false);
            }
        }

        // Notes Handler

        public void HandleAvatarNotesRequest(Object sender, string method, List<String> args)
        {
            string targetid;
            string notes = "";

            if (!(sender is IClientAPI))
                return;

            IClientAPI remoteClient = (IClientAPI)sender;

            Hashtable ReqHash = new Hashtable();

            ReqHash["avatar_id"] = remoteClient.AgentId.ToString();
            ReqHash["uuid"] = args[0];

            Hashtable result = GenericXMLRPCRequest(ReqHash,
                    method);

            if (!Convert.ToBoolean(result["success"]))
            {
                remoteClient.SendAgentAlertMessage(
                        result["errorMessage"].ToString(), false);
                return;
            }

            ArrayList dataArray = (ArrayList)result["data"];

            if (dataArray != null && dataArray[0] != null)
            {
                Hashtable d = (Hashtable)dataArray[0];

                targetid = d["targetid"].ToString();
                if (d["notes"] != null)
                    notes = d["notes"].ToString();

                remoteClient.SendAvatarNotesReply(new UUID(targetid), notes);
            }
        }

        // Notes Update

        public void AvatarNotesUpdate(IClientAPI remoteClient, UUID queryTargetID, string queryNotes)
        {
            Hashtable ReqHash = new Hashtable();

            ReqHash["avatar_id"] = remoteClient.AgentId.ToString();
            ReqHash["target_id"] = queryTargetID.ToString();
            ReqHash["notes"] = queryNotes;

            Hashtable result = GenericXMLRPCRequest(ReqHash,
                    "avatar_notes_update");

            if (!Convert.ToBoolean(result["success"]))
            {
                remoteClient.SendAgentAlertMessage(
                        result["errorMessage"].ToString(), false);
            }
        }

        // Standard Profile bits
        public void AvatarInterestsUpdate(IClientAPI remoteClient, uint wantmask, string wanttext, uint skillsmask, string skillstext, string languages)
        {
            Hashtable ReqHash = new Hashtable();

            ReqHash["avatar_id"] = remoteClient.AgentId.ToString();
            ReqHash["wantmask"] = wantmask.ToString();
            ReqHash["wanttext"] = wanttext;
            ReqHash["skillsmask"] = skillsmask.ToString();
            ReqHash["skillstext"] = skillstext;
            ReqHash["languages"] = languages;

            Hashtable result = GenericXMLRPCRequest(ReqHash,
                    "avatar_interests_update");

            if (!Convert.ToBoolean(result["success"]))
            {
                remoteClient.SendAgentAlertMessage(
                        result["errorMessage"].ToString(), false);
            }
        }

        public void UserPreferencesRequest(IClientAPI remoteClient)
        {
            Hashtable ReqHash = new Hashtable();

            ReqHash["avatar_id"] = remoteClient.AgentId.ToString();

            Hashtable result = GenericXMLRPCRequest(ReqHash,
                    "user_preferences_request");

            if (!Convert.ToBoolean(result["success"]))
            {
                remoteClient.SendAgentAlertMessage(
                        result["errorMessage"].ToString(), false);
                return;
            }

            ArrayList dataArray = (ArrayList)result["data"];

            if (dataArray != null && dataArray[0] != null)
            {
                Hashtable d = (Hashtable)dataArray[0];
                string mail = "";

                if (d["email"] != null)
                    mail = d["email"].ToString();

                remoteClient.SendUserInfoReply(
                        Convert.ToBoolean(d["imviaemail"]),
                        Convert.ToBoolean(d["visible"]),
                        mail);
            }
        }

        public void UpdateUserPreferences(bool imViaEmail, bool visible, IClientAPI remoteClient)
        {
            Hashtable ReqHash = new Hashtable();

            ReqHash["avatar_id"] = remoteClient.AgentId.ToString();
            ReqHash["imViaEmail"] = imViaEmail.ToString();
            ReqHash["visible"] = visible.ToString();

            Hashtable result = GenericXMLRPCRequest(ReqHash,
                    "user_preferences_update");

            if (!Convert.ToBoolean(result["success"]))
            {
                remoteClient.SendAgentAlertMessage(
                        result["errorMessage"].ToString(), false);
            }
        }

        // Profile data like the WebURL
        private Hashtable GetProfileData(UUID userID)
        {
//          m_log.ErrorFormat("GetProfileData for " + userID.ToString());
            Hashtable ReqHash = new Hashtable();

            ReqHash["avatar_id"] = userID.ToString();

            Hashtable result = GenericXMLRPCRequest(ReqHash,
                    "avatar_properties_request");

            ArrayList dataArray = (ArrayList)result["data"];

            if (dataArray != null && dataArray[0] != null)
            {
                Hashtable d = (Hashtable)dataArray[0];
                return d;
            }
            return result;
        }

        public void RequestAvatarProperties(IClientAPI remoteClient, UUID avatarID)
        {
//          m_log.ErrorFormat("[jOpenSim.Profile] RequestAvatarProperties for "+avatarID.ToString());
            IScene s = remoteClient.Scene;
            if (!(s is Scene))
                return;

            Scene scene = (Scene)s;

            UserAccount account = scene.UserAccountService.GetUserAccount(scene.RegionInfo.ScopeID, avatarID);
            if (null != account)
            {
                Byte[] charterMember;
                if (account.UserTitle == "")
                {
                    charterMember = new Byte[1];
                    charterMember[0] = (Byte)((account.UserFlags & 0xf00) >> 8);
                }
                else
                {
                    charterMember = Utils.StringToBytes(account.UserTitle);
                }

                Hashtable profileData = GetProfileData(avatarID);
                string profileUrl = String.Empty;
                string aboutText = String.Empty;
                string firstLifeAboutText = String.Empty;
                UUID image = UUID.Zero;
                UUID firstLifeImage = UUID.Zero;
                UUID partner = UUID.Zero;
                uint   wantMask = 0;
                string wantText = String.Empty;
                uint   skillsMask = 0;
                string skillsText = String.Empty;
                string languages = String.Empty;

                if (profileData["ProfileUrl"] != null)
                    profileUrl = profileData["ProfileUrl"].ToString();
                if (profileData["AboutText"] != null)
                    aboutText = profileData["AboutText"].ToString();
                if (profileData["FirstLifeAboutText"] != null)
                    firstLifeAboutText = profileData["FirstLifeAboutText"].ToString();
                if (profileData["Image"] != null)
                    image = new UUID(profileData["Image"].ToString());
                if (profileData["FirstLifeImage"] != null)
                    firstLifeImage = new UUID(profileData["FirstLifeImage"].ToString());
                if (profileData["Partner"] != null)
                    partner = new UUID(profileData["Partner"].ToString());

//                m_log.ErrorFormat("[jOpenSim.Profile] received Data:");
//                m_log.ErrorFormat("[jOpenSim.Profile] [avatarID]: " + avatarID);
//                m_log.ErrorFormat("[jOpenSim.Profile] profileData[AboutText]: " + aboutText);
//                m_log.ErrorFormat("[jOpenSim.Profile] [Created]: " + Util.ToDateTime(account.Created).ToString("M/d/yyyy"), CultureInfo.InvariantCulture);
//                m_log.ErrorFormat("[jOpenSim.Profile] [charterMember]: " + charterMember);
//                m_log.ErrorFormat("[jOpenSim.Profile] profileData[firstLifeAboutText]: " + firstLifeAboutText);
//                m_log.ErrorFormat("[jOpenSim.Profile] [UserFlags]: " + (uint)(account.UserFlags & 0xff));
//                m_log.ErrorFormat("[jOpenSim.Profile] profileData[firstLifeImage]: " + firstLifeImage);
//                m_log.ErrorFormat("[jOpenSim.Profile] profileData[image]: " + image);
//                m_log.ErrorFormat("[jOpenSim.Profile] profileData[profileUrl]: " + profileUrl);
//                m_log.ErrorFormat("[jOpenSim.Profile] profileData[partner]: " + partner);

                // The PROFILE information is no longer stored in the user
                // account. It now needs to be taken from the XMLRPC
                //
                remoteClient.SendAvatarProperties(avatarID, aboutText,
                          Util.ToDateTime(account.Created).ToString(
                                  "M/d/yyyy", CultureInfo.InvariantCulture),
                          charterMember, firstLifeAboutText,
                          (uint)(account.UserFlags & 0xff),
                          firstLifeImage, image, profileUrl, partner);

                //Viewer expects interest data when it asks for properties.
                if (profileData["wantmask"] != null)
                    wantMask = Convert.ToUInt32(profileData["wantmask"].ToString());
                if (profileData["wanttext"] != null)
                    wantText = profileData["wanttext"].ToString();

                if (profileData["skillsmask"] != null)
                    skillsMask = Convert.ToUInt32(profileData["skillsmask"].ToString());
                if (profileData["skillstext"] != null)
                    skillsText = profileData["skillstext"].ToString();

                if (profileData["languages"] != null)
                    languages = profileData["languages"].ToString();

                remoteClient.SendAvatarInterestsReply(avatarID, wantMask, wantText,
                                                      skillsMask, skillsText, languages);
            }
            else
            {
                m_log.Debug("[AvatarProfilesModule]: Got null for profile for " + avatarID.ToString());
            }
        }

        public void UpdateAvatarProperties(IClientAPI remoteClient, UserProfileData newProfile)
        {
            // if it's the profile of the user requesting the update, then we change only a few things.
            if (remoteClient.AgentId == newProfile.ID)
            {
                Hashtable ReqHash = new Hashtable();

                ReqHash["avatar_id"] = remoteClient.AgentId.ToString();
                ReqHash["ProfileUrl"] = newProfile.ProfileUrl;
                ReqHash["Image"] = newProfile.Image.ToString();
                ReqHash["AboutText"] = newProfile.AboutText;
                ReqHash["FirstLifeImage"] = newProfile.FirstLifeImage.ToString();
                ReqHash["FirstLifeAboutText"] = newProfile.FirstLifeAboutText;

                Hashtable result = GenericXMLRPCRequest(ReqHash,
                        "avatar_properties_update");

                if (!Convert.ToBoolean(result["success"]))
                {
                    remoteClient.SendAgentAlertMessage(
                            result["errorMessage"].ToString(), false);
                }

                RequestAvatarProperties(remoteClient, newProfile.ID);
            }
        }
    }
}
