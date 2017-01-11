<?php

if (!class_exists("ServerLogClear")) {
/**
 * ServerLogClear
 */
class ServerLogClear {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
}}

if (!class_exists("ServerLogClearResponse")) {
/**
 * ServerLogClearResponse
 */
class ServerLogClearResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ServerLogClearResult;
}}

if (!class_exists("ServerSaveLoggingSettings")) {
/**
 * ServerSaveLoggingSettings
 */
class ServerSaveLoggingSettings {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $xml;
}}

if (!class_exists("ServerSaveLoggingSettingsResponse")) {
/**
 * ServerSaveLoggingSettingsResponse
 */
class ServerSaveLoggingSettingsResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ServerSaveLoggingSettingsResult;
}}

if (!class_exists("ServerSaveSettings")) {
/**
 * ServerSaveSettings
 */
class ServerSaveSettings {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $xml;
}}

if (!class_exists("ServerSaveSettingsResponse")) {
/**
 * ServerSaveSettingsResponse
 */
class ServerSaveSettingsResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ServerSaveSettingsResult;
}}

if (!class_exists("ServerSaveSystemInfoXML")) {
/**
 * ServerSaveSystemInfoXML
 */
class ServerSaveSystemInfoXML {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $name;
	/**
	 * @access public
	 * @var sstring
	 */
	public $xml;
}}

if (!class_exists("ServerSaveSystemInfoXMLResponse")) {
/**
 * ServerSaveSystemInfoXMLResponse
 */
class ServerSaveSystemInfoXMLResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ServerSaveSystemInfoXMLResult;
}}

if (!class_exists("SetAssetDirectories")) {
/**
 * SetAssetDirectories
 */
class SetAssetDirectories {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $userAssetDirectory;
	/**
	 * @access public
	 * @var sstring
	 */
	public $userGroupAssetDirectory;
	/**
	 * @access public
	 * @var sstring
	 */
	public $documentAssetDirectory;
}}

if (!class_exists("SetAssetDirectoriesResponse")) {
/**
 * SetAssetDirectoriesResponse
 */
class SetAssetDirectoriesResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $SetAssetDirectoriesResult;
}}

if (!class_exists("SetAutomaticPreviewGeneration")) {
/**
 * SetAutomaticPreviewGeneration
 */
class SetAutomaticPreviewGeneration {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sboolean
	 */
	public $createPreviews;
}}

if (!class_exists("SetAutomaticPreviewGenerationResponse")) {
/**
 * SetAutomaticPreviewGenerationResponse
 */
class SetAutomaticPreviewGenerationResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $SetAutomaticPreviewGenerationResult;
}}

if (!class_exists("SetContentAdministration")) {
/**
 * SetContentAdministration
 */
class SetContentAdministration {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sboolean
	 */
	public $allowContentAdministration;
}}

if (!class_exists("SetContentAdministrationResponse")) {
/**
 * SetContentAdministrationResponse
 */
class SetContentAdministrationResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $SetContentAdministrationResult;
}}

if (!class_exists("SetUserLanguage")) {
/**
 * SetUserLanguage
 */
class SetUserLanguage {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $languageIdOrName;
	/**
	 * @access public
	 * @var sboolean
	 */
	public $ignoreWorkSpaceLanguage;
}}

if (!class_exists("SetUserLanguageResponse")) {
/**
 * SetUserLanguageResponse
 */
class SetUserLanguageResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $SetUserLanguageResult;
}}

if (!class_exists("SetWorkingEnvironment")) {
/**
 * SetWorkingEnvironment
 */
class SetWorkingEnvironment {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $environmentName;
}}

if (!class_exists("SetWorkingEnvironmentResponse")) {
/**
 * SetWorkingEnvironmentResponse
 */
class SetWorkingEnvironmentResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $SetWorkingEnvironmentResult;
}}

if (!class_exists("SetWorkspaceAdministration")) {
/**
 * SetWorkspaceAdministration
 */
class SetWorkspaceAdministration {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sboolean
	 */
	public $allowWorkspaceAdministration;
}}

if (!class_exists("SetWorkspaceAdministrationResponse")) {
/**
 * SetWorkspaceAdministrationResponse
 */
class SetWorkspaceAdministrationResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $SetWorkspaceAdministrationResult;
}}

if (!class_exists("SpellCheckDictionariesGetSystemList")) {
/**
 * SpellCheckDictionariesGetSystemList
 */
class SpellCheckDictionariesGetSystemList {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
}}

if (!class_exists("SpellCheckDictionariesGetSystemListResponse")) {
/**
 * SpellCheckDictionariesGetSystemListResponse
 */
class SpellCheckDictionariesGetSystemListResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $SpellCheckDictionariesGetSystemListResult;
}}

if (!class_exists("SpellCheckDictionaryAdd")) {
/**
 * SpellCheckDictionaryAdd
 */
class SpellCheckDictionaryAdd {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $name;
	/**
	 * @access public
	 * @var sstring
	 */
	public $dicFileOrData;
	/**
	 * @access public
	 * @var sstring
	 */
	public $affFileOrData;
}}

if (!class_exists("SpellCheckDictionaryAddResponse")) {
/**
 * SpellCheckDictionaryAddResponse
 */
class SpellCheckDictionaryAddResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $SpellCheckDictionaryAddResult;
}}

if (!class_exists("SpellCheckDictionaryAddFromSystem")) {
/**
 * SpellCheckDictionaryAddFromSystem
 */
class SpellCheckDictionaryAddFromSystem {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $name;
	/**
	 * @access public
	 * @var sstring
	 */
	public $systemDictName;
}}

if (!class_exists("SpellCheckDictionaryAddFromSystemResponse")) {
/**
 * SpellCheckDictionaryAddFromSystemResponse
 */
class SpellCheckDictionaryAddFromSystemResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $SpellCheckDictionaryAddFromSystemResult;
}}

if (!class_exists("SpellCheckDictionaryReplaceFile")) {
/**
 * SpellCheckDictionaryReplaceFile
 */
class SpellCheckDictionaryReplaceFile {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $itemID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $fileType;
	/**
	 * @access public
	 * @var sstring
	 */
	public $fileOrData;
}}

if (!class_exists("SpellCheckDictionaryReplaceFileResponse")) {
/**
 * SpellCheckDictionaryReplaceFileResponse
 */
class SpellCheckDictionaryReplaceFileResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $SpellCheckDictionaryReplaceFileResult;
}}

if (!class_exists("SwitchServerFlowGetCheckPoints")) {
/**
 * SwitchServerFlowGetCheckPoints
 */
class SwitchServerFlowGetCheckPoints {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $switchServerID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $flowID;
}}

if (!class_exists("SwitchServerFlowGetCheckPointsResponse")) {
/**
 * SwitchServerFlowGetCheckPointsResponse
 */
class SwitchServerFlowGetCheckPointsResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $SwitchServerFlowGetCheckPointsResult;
}}

if (!class_exists("SwitchServerFlowGetElementsJobCount")) {
/**
 * SwitchServerFlowGetElementsJobCount
 */
class SwitchServerFlowGetElementsJobCount {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $switchServerID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $flowID;
}}

if (!class_exists("SwitchServerFlowGetElementsJobCountResponse")) {
/**
 * SwitchServerFlowGetElementsJobCountResponse
 */
class SwitchServerFlowGetElementsJobCountResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $SwitchServerFlowGetElementsJobCountResult;
}}

if (!class_exists("SwitchServerFlowGetFullConfig")) {
/**
 * SwitchServerFlowGetFullConfig
 */
class SwitchServerFlowGetFullConfig {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $switchServerID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $flowID;
}}

if (!class_exists("SwitchServerFlowGetFullConfigResponse")) {
/**
 * SwitchServerFlowGetFullConfigResponse
 */
class SwitchServerFlowGetFullConfigResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $SwitchServerFlowGetFullConfigResult;
}}

if (!class_exists("SwitchServerFlowGetJobs")) {
/**
 * SwitchServerFlowGetJobs
 */
class SwitchServerFlowGetJobs {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $switchServerID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $flowID;
}}

if (!class_exists("SwitchServerFlowGetJobsResponse")) {
/**
 * SwitchServerFlowGetJobsResponse
 */
class SwitchServerFlowGetJobsResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $SwitchServerFlowGetJobsResult;
}}

if (!class_exists("SwitchServerFlowGetSubmitPoints")) {
/**
 * SwitchServerFlowGetSubmitPoints
 */
class SwitchServerFlowGetSubmitPoints {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $switchServerID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $flowID;
}}

if (!class_exists("SwitchServerFlowGetSubmitPointsResponse")) {
/**
 * SwitchServerFlowGetSubmitPointsResponse
 */
class SwitchServerFlowGetSubmitPointsResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $SwitchServerFlowGetSubmitPointsResult;
}}

if (!class_exists("SwitchServerFlowSubmitFileToFolder")) {
/**
 * SwitchServerFlowSubmitFileToFolder
 */
class SwitchServerFlowSubmitFileToFolder {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $switchServerID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $flowID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $elementID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $filePathOrData;
	/**
	 * @access public
	 * @var sstring
	 */
	public $fileName;
}}

if (!class_exists("SwitchServerFlowSubmitFileToFolderResponse")) {
/**
 * SwitchServerFlowSubmitFileToFolderResponse
 */
class SwitchServerFlowSubmitFileToFolderResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $SwitchServerFlowSubmitFileToFolderResult;
}}

if (!class_exists("SwitchServerFlowSubmitFileToSubmitPoint")) {
/**
 * SwitchServerFlowSubmitFileToSubmitPoint
 */
class SwitchServerFlowSubmitFileToSubmitPoint {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $switchServerID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $flowID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $elementID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $filePathOrData;
	/**
	 * @access public
	 * @var sstring
	 */
	public $fileName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $metaXML;
}}

if (!class_exists("SwitchServerFlowSubmitFileToSubmitPointResponse")) {
/**
 * SwitchServerFlowSubmitFileToSubmitPointResponse
 */
class SwitchServerFlowSubmitFileToSubmitPointResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $SwitchServerFlowSubmitFileToSubmitPointResult;
}}

if (!class_exists("SwitchServerGetFlowList")) {
/**
 * SwitchServerGetFlowList
 */
class SwitchServerGetFlowList {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $switchServerID;
}}

if (!class_exists("SwitchServerGetFlowListResponse")) {
/**
 * SwitchServerGetFlowListResponse
 */
class SwitchServerGetFlowListResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $SwitchServerGetFlowListResult;
}}

if (!class_exists("SwitchServerTestConnection")) {
/**
 * SwitchServerTestConnection
 */
class SwitchServerTestConnection {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $url;
	/**
	 * @access public
	 * @var sstring
	 */
	public $userName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $userPW;
	/**
	 * @access public
	 * @var sstring
	 */
	public $oemKey;
}}

if (!class_exists("SwitchServerTestConnectionResponse")) {
/**
 * SwitchServerTestConnectionResponse
 */
class SwitchServerTestConnectionResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $SwitchServerTestConnectionResult;
}}

if (!class_exists("TaskGetEditorCliLog")) {
/**
 * TaskGetEditorCliLog
 */
class TaskGetEditorCliLog {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $taskID;
}}

if (!class_exists("TaskGetEditorCliLogResponse")) {
/**
 * TaskGetEditorCliLogResponse
 */
class TaskGetEditorCliLogResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $TaskGetEditorCliLogResult;
}}

if (!class_exists("TaskGetStatus")) {
/**
 * TaskGetStatus
 */
class TaskGetStatus {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $taskID;
}}

if (!class_exists("TaskGetStatusResponse")) {
/**
 * TaskGetStatusResponse
 */
class TaskGetStatusResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $TaskGetStatusResult;
}}

if (!class_exists("TaskGetStatusAndRemoveIfCompleted")) {
/**
 * TaskGetStatusAndRemoveIfCompleted
 */
class TaskGetStatusAndRemoveIfCompleted {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $taskID;
}}

if (!class_exists("TaskGetStatusAndRemoveIfCompletedResponse")) {
/**
 * TaskGetStatusAndRemoveIfCompletedResponse
 */
class TaskGetStatusAndRemoveIfCompletedResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $TaskGetStatusAndRemoveIfCompletedResult;
}}

if (!class_exists("TaskRemoveFromLog")) {
/**
 * TaskRemoveFromLog
 */
class TaskRemoveFromLog {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $taskID;
}}

if (!class_exists("TaskRemoveFromLogResponse")) {
/**
 * TaskRemoveFromLogResponse
 */
class TaskRemoveFromLogResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $TaskRemoveFromLogResult;
}}

if (!class_exists("TasksGetList")) {
/**
 * TasksGetList
 */
class TasksGetList {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sboolean
	 */
	public $includeRunningTasks;
	/**
	 * @access public
	 * @var sboolean
	 */
	public $includeWaitingTasks;
	/**
	 * @access public
	 * @var sboolean
	 */
	public $includeFinishedTasks;
}}

if (!class_exists("TasksGetListResponse")) {
/**
 * TasksGetListResponse
 */
class TasksGetListResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $TasksGetListResult;
}}

if (!class_exists("TasksGetQueueOverview")) {
/**
 * TasksGetQueueOverview
 */
class TasksGetQueueOverview {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
}}

if (!class_exists("TasksGetQueueOverviewResponse")) {
/**
 * TasksGetQueueOverviewResponse
 */
class TasksGetQueueOverviewResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $TasksGetQueueOverviewResult;
}}

if (!class_exists("TasksGetStatusses")) {
/**
 * TasksGetStatusses
 */
class TasksGetStatusses {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $taskXML;
}}

if (!class_exists("TasksGetStatussesResponse")) {
/**
 * TasksGetStatussesResponse
 */
class TasksGetStatussesResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $TasksGetStatussesResult;
}}

if (!class_exists("UploadExternalAsset")) {
/**
 * UploadExternalAsset
 */
class UploadExternalAsset {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $url;
	/**
	 * @access public
	 * @var sstring
	 */
	public $fileName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $fileData;
}}

if (!class_exists("UploadExternalAssetResponse")) {
/**
 * UploadExternalAssetResponse
 */
class UploadExternalAssetResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $UploadExternalAssetResult;
}}

if (!class_exists("XinetExecutePortalDICall")) {
/**
 * XinetExecutePortalDICall
 */
class XinetExecutePortalDICall {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $xinetServerID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $callID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $arguments;
}}

if (!class_exists("XinetExecutePortalDICallResponse")) {
/**
 * XinetExecutePortalDICallResponse
 */
class XinetExecutePortalDICallResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $XinetExecutePortalDICallResult;
}}

if (!class_exists("XinetSetCurrentCredentials")) {
/**
 * XinetSetCurrentCredentials
 */
class XinetSetCurrentCredentials {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $userName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $userPW;
}}

if (!class_exists("XinetSetCurrentCredentialsResponse")) {
/**
 * XinetSetCurrentCredentialsResponse
 */
class XinetSetCurrentCredentialsResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $XinetSetCurrentCredentialsResult;
}}

if (!class_exists("XinetTestConnection")) {
/**
 * XinetTestConnection
 */
class XinetTestConnection {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $url;
	/**
	 * @access public
	 * @var sstring
	 */
	public $userName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $userPW;
}}

if (!class_exists("XinetTestConnectionResponse")) {
/**
 * XinetTestConnectionResponse
 */
class XinetTestConnectionResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $XinetTestConnectionResult;
}}

if (!class_exists("AdsGetFromURL")) {
/**
 * AdsGetFromURL
 */
class AdsGetFromURL {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $url;
}}

if (!class_exists("AdsGetFromURLResponse")) {
/**
 * AdsGetFromURLResponse
 */
class AdsGetFromURLResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $AdsGetFromURLResult;
}}

if (!class_exists("ApiKeyGetCurrentSettings")) {
/**
 * ApiKeyGetCurrentSettings
 */
class ApiKeyGetCurrentSettings {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
}}

if (!class_exists("ApiKeyGetCurrentSettingsResponse")) {
/**
 * ApiKeyGetCurrentSettingsResponse
 */
class ApiKeyGetCurrentSettingsResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ApiKeyGetCurrentSettingsResult;
}}

if (!class_exists("ApiKeyKeepAlive")) {
/**
 * ApiKeyKeepAlive
 */
class ApiKeyKeepAlive {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
}}

if (!class_exists("ApiKeyKeepAliveResponse")) {
/**
 * ApiKeyKeepAliveResponse
 */
class ApiKeyKeepAliveResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ApiKeyKeepAliveResult;
}}

if (!class_exists("AssetGetImageInfo")) {
/**
 * AssetGetImageInfo
 */
class AssetGetImageInfo {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $assetID;
}}

if (!class_exists("AssetGetImageInfoResponse")) {
/**
 * AssetGetImageInfoResponse
 */
class AssetGetImageInfoResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $AssetGetImageInfoResult;
}}

if (!class_exists("BarcodeCreate")) {
/**
 * BarcodeCreate
 */
class BarcodeCreate {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $barcodeTypeID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $barcodeText;
}}

if (!class_exists("BarcodeCreateResponse")) {
/**
 * BarcodeCreateResponse
 */
class BarcodeCreateResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $BarcodeCreateResult;
}}

if (!class_exists("BarcodeCreateColored")) {
/**
 * BarcodeCreateColored
 */
class BarcodeCreateColored {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $barcodeTypeID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $barcodeText;
	/**
	 * @access public
	 * @var sstring
	 */
	public $backColor;
	/**
	 * @access public
	 * @var sstring
	 */
	public $barColor;
	/**
	 * @access public
	 * @var sstring
	 */
	public $textColor;
}}

if (!class_exists("BarcodeCreateColoredResponse")) {
/**
 * BarcodeCreateColoredResponse
 */
class BarcodeCreateColoredResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $BarcodeCreateColoredResult;
}}

if (!class_exists("CsvFileCreate")) {
/**
 * CsvFileCreate
 */
class CsvFileCreate {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $xmlData;
	/**
	 * @access public
	 * @var sstring
	 */
	public $fileName;
}}

if (!class_exists("CsvFileCreateResponse")) {
/**
 * CsvFileCreateResponse
 */
class CsvFileCreateResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $CsvFileCreateResult;
}}

if (!class_exists("DataSourceAddSampleFile")) {
/**
 * DataSourceAddSampleFile
 */
class DataSourceAddSampleFile {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $dataSourceID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $fileName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $fileOrData;
}}

if (!class_exists("DataSourceAddSampleFileResponse")) {
/**
 * DataSourceAddSampleFileResponse
 */
class DataSourceAddSampleFileResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $DataSourceAddSampleFileResult;
}}

if (!class_exists("DataSourceDeleteSampleFile")) {
/**
 * DataSourceDeleteSampleFile
 */
class DataSourceDeleteSampleFile {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $dataSourceID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $fileName;
}}

if (!class_exists("DataSourceDeleteSampleFileResponse")) {
/**
 * DataSourceDeleteSampleFileResponse
 */
class DataSourceDeleteSampleFileResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $DataSourceDeleteSampleFileResult;
}}

if (!class_exists("DataSourceDownloadSpreadsheets")) {
/**
 * DataSourceDownloadSpreadsheets
 */
class DataSourceDownloadSpreadsheets {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $dataSourceID;
}}

if (!class_exists("DataSourceDownloadSpreadsheetsResponse")) {
/**
 * DataSourceDownloadSpreadsheetsResponse
 */
class DataSourceDownloadSpreadsheetsResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $DataSourceDownloadSpreadsheetsResult;
}}

if (!class_exists("DataSourceDownloadURL")) {
/**
 * DataSourceDownloadURL
 */
class DataSourceDownloadURL {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $dataSourceID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $urlType;
	/**
	 * @access public
	 * @var sstring
	 */
	public $query;
	/**
	 * @access public
	 * @var sstring
	 */
	public $forDocumentID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $editorQueryString;
}}

if (!class_exists("DataSourceDownloadURLResponse")) {
/**
 * DataSourceDownloadURLResponse
 */
class DataSourceDownloadURLResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $DataSourceDownloadURLResult;
}}

if (!class_exists("DataSourceFileGetXML")) {
/**
 * DataSourceFileGetXML
 */
class DataSourceFileGetXML {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $dataSourceID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $fileDataOrPath;
	/**
	 * @access public
	 * @var sstring
	 */
	public $fileExtension;
}}

if (!class_exists("DataSourceFileGetXMLResponse")) {
/**
 * DataSourceFileGetXMLResponse
 */
class DataSourceFileGetXMLResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $DataSourceFileGetXMLResult;
}}

if (!class_exists("DataSourceListSampleFiles")) {
/**
 * DataSourceListSampleFiles
 */
class DataSourceListSampleFiles {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $dataSourceID;
}}

if (!class_exists("DataSourceListSampleFilesResponse")) {
/**
 * DataSourceListSampleFilesResponse
 */
class DataSourceListSampleFilesResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $DataSourceListSampleFilesResult;
}}

if (!class_exists("DataSourceSalesForceGetXML")) {
/**
 * DataSourceSalesForceGetXML
 */
class DataSourceSalesForceGetXML {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $dataSourceID;
}}

if (!class_exists("DataSourceSalesForceGetXMLResponse")) {
/**
 * DataSourceSalesForceGetXMLResponse
 */
class DataSourceSalesForceGetXMLResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $DataSourceSalesForceGetXMLResult;
}}

if (!class_exists("DataSourceSpreadsheetGetXML")) {
/**
 * DataSourceSpreadsheetGetXML
 */
class DataSourceSpreadsheetGetXML {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $dataSourceID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $spreadsheetID;
}}

if (!class_exists("DataSourceSpreadsheetGetXMLResponse")) {
/**
 * DataSourceSpreadsheetGetXMLResponse
 */
class DataSourceSpreadsheetGetXMLResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $DataSourceSpreadsheetGetXMLResult;
}}

if (!class_exists("DocumentCopyAnnotations")) {
/**
 * DocumentCopyAnnotations
 */
class DocumentCopyAnnotations {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $fromItemID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $toItemID;
	/**
	 * @access public
	 * @var sboolean
	 */
	public $replaceExistingAnnotations;
}}

if (!class_exists("DocumentCopyAnnotationsResponse")) {
/**
 * DocumentCopyAnnotationsResponse
 */
class DocumentCopyAnnotationsResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $DocumentCopyAnnotationsResult;
}}

if (!class_exists("DocumentCopyDocumentEventActions")) {
/**
 * DocumentCopyDocumentEventActions
 */
class DocumentCopyDocumentEventActions {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $fromItemID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $toItemID;
	/**
	 * @access public
	 * @var sboolean
	 */
	public $replaceExistingActions;
}}

if (!class_exists("DocumentCopyDocumentEventActionsResponse")) {
/**
 * DocumentCopyDocumentEventActionsResponse
 */
class DocumentCopyDocumentEventActionsResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $DocumentCopyDocumentEventActionsResult;
}}

if (!class_exists("DocumentCopyVariableDefinitions")) {
/**
 * DocumentCopyVariableDefinitions
 */
class DocumentCopyVariableDefinitions {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $fromItemID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $toItemID;
	/**
	 * @access public
	 * @var sboolean
	 */
	public $replaceExistingVariables;
}}

if (!class_exists("DocumentCopyVariableDefinitionsResponse")) {
/**
 * DocumentCopyVariableDefinitionsResponse
 */
class DocumentCopyVariableDefinitionsResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $DocumentCopyVariableDefinitionsResult;
}}

if (!class_exists("DocumentCreateFromBlankDocTemplate")) {
/**
 * DocumentCreateFromBlankDocTemplate
 */
class DocumentCreateFromBlankDocTemplate {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $documentName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $folderPath;
	/**
	 * @access public
	 * @var sstring
	 */
	public $blankDocTemplateID;
}}

if (!class_exists("DocumentCreateFromBlankDocTemplateResponse")) {
/**
 * DocumentCreateFromBlankDocTemplateResponse
 */
class DocumentCreateFromBlankDocTemplateResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $DocumentCreateFromBlankDocTemplateResult;
}}

if (!class_exists("DocumentCreateFromChiliPackage")) {
/**
 * DocumentCreateFromChiliPackage
 */
class DocumentCreateFromChiliPackage {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $documentName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $folderPath;
	/**
	 * @access public
	 * @var sstring
	 */
	public $packagePathOrData;
	/**
	 * @access public
	 * @var sstring
	 */
	public $newAssetLocation;
	/**
	 * @access public
	 * @var sstring
	 */
	public $newFontLocation;
}}

if (!class_exists("DocumentCreateFromChiliPackageResponse")) {
/**
 * DocumentCreateFromChiliPackageResponse
 */
class DocumentCreateFromChiliPackageResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $DocumentCreateFromChiliPackageResult;
}}

if (!class_exists("DocumentCreateFromPDF")) {
/**
 * DocumentCreateFromPDF
 */
class DocumentCreateFromPDF {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $documentName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $folderPath;
	/**
	 * @access public
	 * @var sstring
	 */
	public $pdfPathOrData;
	/**
	 * @access public
	 * @var sstring
	 */
	public $backgroundAssetLocation;
}}

if (!class_exists("DocumentCreateFromPDFResponse")) {
/**
 * DocumentCreateFromPDFResponse
 */
class DocumentCreateFromPDFResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $DocumentCreateFromPDFResult;
}}

if (!class_exists("DocumentCreateImages")) {
/**
 * DocumentCreateImages
 */
class DocumentCreateImages {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $itemID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $settingsXML;
	/**
	 * @access public
	 * @var sstring
	 */
	public $imageConversionProfileID;
	/**
	 * @access public
	 * @var sint
	 */
	public $taskPriority;
}}

if (!class_exists("DocumentCreateImagesResponse")) {
/**
 * DocumentCreateImagesResponse
 */
class DocumentCreateImagesResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $DocumentCreateImagesResult;
}}

if (!class_exists("DocumentCreateImagesAndPDF")) {
/**
 * DocumentCreateImagesAndPDF
 */
class DocumentCreateImagesAndPDF {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $itemID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $settingsXML;
	/**
	 * @access public
	 * @var sstring
	 */
	public $imageConversionProfileID;
	/**
	 * @access public
	 * @var sint
	 */
	public $taskPriority;
}}

if (!class_exists("DocumentCreateImagesAndPDFResponse")) {
/**
 * DocumentCreateImagesAndPDFResponse
 */
class DocumentCreateImagesAndPDFResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $DocumentCreateImagesAndPDFResult;
}}

if (!class_exists("DocumentCreatePackage")) {
/**
 * DocumentCreatePackage
 */
class DocumentCreatePackage {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $itemID;
	/**
	 * @access public
	 * @var sint
	 */
	public $taskPriority;
}}

if (!class_exists("DocumentCreatePackageResponse")) {
/**
 * DocumentCreatePackageResponse
 */
class DocumentCreatePackageResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $DocumentCreatePackageResult;
}}

if (!class_exists("DocumentCreatePDF")) {
/**
 * DocumentCreatePDF
 */
class DocumentCreatePDF {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $itemID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $settingsXML;
	/**
	 * @access public
	 * @var sint
	 */
	public $taskPriority;
}}

if (!class_exists("DocumentCreatePDFResponse")) {
/**
 * DocumentCreatePDFResponse
 */
class DocumentCreatePDFResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $DocumentCreatePDFResult;
}}

if (!class_exists("DocumentCreateTempFolding")) {
/**
 * DocumentCreateTempFolding
 */
class DocumentCreateTempFolding {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $itemID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $docXML;
	/**
	 * @access public
	 * @var sint
	 */
	public $taskPriority;
}}

if (!class_exists("DocumentCreateTempFoldingResponse")) {
/**
 * DocumentCreateTempFoldingResponse
 */
class DocumentCreateTempFoldingResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $DocumentCreateTempFoldingResult;
}}

if (!class_exists("DocumentCreateTempImages")) {
/**
 * DocumentCreateTempImages
 */
class DocumentCreateTempImages {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $itemID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $docXML;
	/**
	 * @access public
	 * @var sstring
	 */
	public $settingsXML;
	/**
	 * @access public
	 * @var sstring
	 */
	public $imageConversionProfileID;
	/**
	 * @access public
	 * @var sint
	 */
	public $taskPriority;
}}

if (!class_exists("DocumentCreateTempImagesResponse")) {
/**
 * DocumentCreateTempImagesResponse
 */
class DocumentCreateTempImagesResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $DocumentCreateTempImagesResult;
}}

if (!class_exists("DocumentCreateTempImagesAndPDF")) {
/**
 * DocumentCreateTempImagesAndPDF
 */
class DocumentCreateTempImagesAndPDF {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $itemID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $docXML;
	/**
	 * @access public
	 * @var sstring
	 */
	public $settingsXML;
	/**
	 * @access public
	 * @var sstring
	 */
	public $imageConversionProfileID;
	/**
	 * @access public
	 * @var sint
	 */
	public $taskPriority;
}}

if (!class_exists("DocumentCreateTempImagesAndPDFResponse")) {
/**
 * DocumentCreateTempImagesAndPDFResponse
 */
class DocumentCreateTempImagesAndPDFResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $DocumentCreateTempImagesAndPDFResult;
}}

if (!class_exists("DocumentCreateTempPackage")) {
/**
 * DocumentCreateTempPackage
 */
class DocumentCreateTempPackage {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $itemID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $docXML;
	/**
	 * @access public
	 * @var sint
	 */
	public $taskPriority;
}}

if (!class_exists("DocumentCreateTempPackageResponse")) {
/**
 * DocumentCreateTempPackageResponse
 */
class DocumentCreateTempPackageResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $DocumentCreateTempPackageResult;
}}

if (!class_exists("DocumentCreateTempPDF")) {
/**
 * DocumentCreateTempPDF
 */
class DocumentCreateTempPDF {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $itemID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $docXML;
	/**
	 * @access public
	 * @var sstring
	 */
	public $settingsXML;
	/**
	 * @access public
	 * @var sint
	 */
	public $taskPriority;
}}

if (!class_exists("DocumentCreateTempPDFResponse")) {
/**
 * DocumentCreateTempPDFResponse
 */
class DocumentCreateTempPDFResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $DocumentCreateTempPDFResult;
}}

if (!class_exists("DocumentGetAnnotations")) {
/**
 * DocumentGetAnnotations
 */
class DocumentGetAnnotations {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $itemID;
}}

if (!class_exists("DocumentGetAnnotationsResponse")) {
/**
 * DocumentGetAnnotationsResponse
 */
class DocumentGetAnnotationsResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $DocumentGetAnnotationsResult;
}}

if (!class_exists("DocumentGetDefaultSettings")) {
/**
 * DocumentGetDefaultSettings
 */
class DocumentGetDefaultSettings {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $itemID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $viewType;
	/**
	 * @access public
	 * @var sstring
	 */
	public $viewPrefsID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $constraintID;
}}

if (!class_exists("DocumentGetDefaultSettingsResponse")) {
/**
 * DocumentGetDefaultSettingsResponse
 */
class DocumentGetDefaultSettingsResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $DocumentGetDefaultSettingsResult;
}}

if (!class_exists("DocumentGetDocumentEventActions")) {
/**
 * DocumentGetDocumentEventActions
 */
class DocumentGetDocumentEventActions {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $itemID;
}}

if (!class_exists("DocumentGetDocumentEventActionsResponse")) {
/**
 * DocumentGetDocumentEventActionsResponse
 */
class DocumentGetDocumentEventActionsResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $DocumentGetDocumentEventActionsResult;
}}

if (!class_exists("DocumentGetEditorURL")) {
/**
 * DocumentGetEditorURL
 */
class DocumentGetEditorURL {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $itemID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $workSpaceID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $viewPrefsID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $constraintsID;
	/**
	 * @access public
	 * @var sboolean
	 */
	public $viewerOnly;
	/**
	 * @access public
	 * @var sboolean
	 */
	public $forAnonymousUser;
}}

if (!class_exists("DocumentGetEditorURLResponse")) {
/**
 * DocumentGetEditorURLResponse
 */
class DocumentGetEditorURLResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $DocumentGetEditorURLResult;
}}

if (!class_exists("DocumentGetFoldingViewerURL")) {
/**
 * DocumentGetFoldingViewerURL
 */
class DocumentGetFoldingViewerURL {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $itemID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $foldingSettingsID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $modXML;
}}

if (!class_exists("DocumentGetFoldingViewerURLResponse")) {
/**
 * DocumentGetFoldingViewerURLResponse
 */
class DocumentGetFoldingViewerURLResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $DocumentGetFoldingViewerURLResult;
}}

if (!class_exists("DocumentGetInfo")) {
/**
 * DocumentGetInfo
 */
class DocumentGetInfo {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $itemID;
	/**
	 * @access public
	 * @var sboolean
	 */
	public $extended;
}}

if (!class_exists("DocumentGetInfoResponse")) {
/**
 * DocumentGetInfoResponse
 */
class DocumentGetInfoResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $DocumentGetInfoResult;
}}

if (!class_exists("DocumentGetIpadXML")) {
/**
 * DocumentGetIpadXML
 */
class DocumentGetIpadXML {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $itemID;
}}

if (!class_exists("DocumentGetIpadXMLResponse")) {
/**
 * DocumentGetIpadXMLResponse
 */
class DocumentGetIpadXMLResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $DocumentGetIpadXMLResult;
}}

if (!class_exists("DocumentGetPlacedAdsAndEdit")) {
/**
 * DocumentGetPlacedAdsAndEdit
 */
class DocumentGetPlacedAdsAndEdit {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $itemID;
}}

if (!class_exists("DocumentGetPlacedAdsAndEditResponse")) {
/**
 * DocumentGetPlacedAdsAndEditResponse
 */
class DocumentGetPlacedAdsAndEditResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $DocumentGetPlacedAdsAndEditResult;
}}

if (!class_exists("DocumentGetPreflightResults")) {
/**
 * DocumentGetPreflightResults
 */
class DocumentGetPreflightResults {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $itemID;
}}

if (!class_exists("DocumentGetPreflightResultsResponse")) {
/**
 * DocumentGetPreflightResultsResponse
 */
class DocumentGetPreflightResultsResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $DocumentGetPreflightResultsResult;
}}

if (!class_exists("DocumentGetUsedAssets")) {
/**
 * DocumentGetUsedAssets
 */
class DocumentGetUsedAssets {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $itemID;
}}

if (!class_exists("DocumentGetUsedAssetsResponse")) {
/**
 * DocumentGetUsedAssetsResponse
 */
class DocumentGetUsedAssetsResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $DocumentGetUsedAssetsResult;
}}

if (!class_exists("DocumentGetVariableDefinitions")) {
/**
 * DocumentGetVariableDefinitions
 */
class DocumentGetVariableDefinitions {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $itemID;
}}

if (!class_exists("DocumentGetVariableDefinitionsResponse")) {
/**
 * DocumentGetVariableDefinitionsResponse
 */
class DocumentGetVariableDefinitionsResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $DocumentGetVariableDefinitionsResult;
}}

if (!class_exists("DocumentGetVariableValues")) {
/**
 * DocumentGetVariableValues
 */
class DocumentGetVariableValues {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $itemID;
}}

if (!class_exists("DocumentGetVariableValuesResponse")) {
/**
 * DocumentGetVariableValuesResponse
 */
class DocumentGetVariableValuesResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $DocumentGetVariableValuesResult;
}}

if (!class_exists("DocumentSetAnnotations")) {
/**
 * DocumentSetAnnotations
 */
class DocumentSetAnnotations {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $itemID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $annotationXML;
	/**
	 * @access public
	 * @var sboolean
	 */
	public $replaceExistingAnnotations;
}}

if (!class_exists("DocumentSetAnnotationsResponse")) {
/**
 * DocumentSetAnnotationsResponse
 */
class DocumentSetAnnotationsResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $DocumentSetAnnotationsResult;
}}

if (!class_exists("DocumentSetAssetDirectories")) {
/**
 * DocumentSetAssetDirectories
 */
class DocumentSetAssetDirectories {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $documentID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $userAssetDirectory;
	/**
	 * @access public
	 * @var sstring
	 */
	public $userGroupAssetDirectory;
	/**
	 * @access public
	 * @var sstring
	 */
	public $documentAssetDirectory;
}}

if (!class_exists("DocumentSetAssetDirectoriesResponse")) {
/**
 * DocumentSetAssetDirectoriesResponse
 */
class DocumentSetAssetDirectoriesResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $DocumentSetAssetDirectoriesResult;
}}

if (!class_exists("DocumentSetConstraints")) {
/**
 * DocumentSetConstraints
 */
class DocumentSetConstraints {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $documentID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $constraintsID;
}}

if (!class_exists("DocumentSetConstraintsResponse")) {
/**
 * DocumentSetConstraintsResponse
 */
class DocumentSetConstraintsResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $DocumentSetConstraintsResult;
}}

if (!class_exists("DocumentSetDocumentEventActions")) {
/**
 * DocumentSetDocumentEventActions
 */
class DocumentSetDocumentEventActions {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $itemID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $definitionXML;
	/**
	 * @access public
	 * @var sboolean
	 */
	public $replaceExistingActions;
}}

if (!class_exists("DocumentSetDocumentEventActionsResponse")) {
/**
 * DocumentSetDocumentEventActionsResponse
 */
class DocumentSetDocumentEventActionsResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $DocumentSetDocumentEventActionsResult;
}}

if (!class_exists("DocumentSetVariableDefinitions")) {
/**
 * DocumentSetVariableDefinitions
 */
class DocumentSetVariableDefinitions {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $itemID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $definitionXML;
	/**
	 * @access public
	 * @var sboolean
	 */
	public $replaceExistingVariables;
}}

if (!class_exists("DocumentSetVariableDefinitionsResponse")) {
/**
 * DocumentSetVariableDefinitionsResponse
 */
class DocumentSetVariableDefinitionsResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $DocumentSetVariableDefinitionsResult;
}}

if (!class_exists("DocumentSetVariableValues")) {
/**
 * DocumentSetVariableValues
 */
class DocumentSetVariableValues {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $itemID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $varXML;
}}

if (!class_exists("DocumentSetVariableValuesResponse")) {
/**
 * DocumentSetVariableValuesResponse
 */
class DocumentSetVariableValuesResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $DocumentSetVariableValuesResult;
}}

if (!class_exists("DownloadURL")) {
/**
 * DownloadURL
 */
class DownloadURL {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $url;
}}

if (!class_exists("DownloadURLResponse")) {
/**
 * DownloadURLResponse
 */
class DownloadURLResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $DownloadURLResult;
}}

if (!class_exists("EditsGetFromURL")) {
/**
 * EditsGetFromURL
 */
class EditsGetFromURL {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $url;
}}

if (!class_exists("EditsGetFromURLResponse")) {
/**
 * EditsGetFromURLResponse
 */
class EditsGetFromURLResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $EditsGetFromURLResult;
}}

if (!class_exists("EnvironmentAdd")) {
/**
 * EnvironmentAdd
 */
class EnvironmentAdd {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $newName;
}}

if (!class_exists("EnvironmentAddResponse")) {
/**
 * EnvironmentAddResponse
 */
class EnvironmentAddResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $EnvironmentAddResult;
}}

if (!class_exists("EnvironmentCopy")) {
/**
 * EnvironmentCopy
 */
class EnvironmentCopy {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $environmentName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $newName;
}}

if (!class_exists("EnvironmentCopyResponse")) {
/**
 * EnvironmentCopyResponse
 */
class EnvironmentCopyResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $EnvironmentCopyResult;
}}

if (!class_exists("EnvironmentDelete")) {
/**
 * EnvironmentDelete
 */
class EnvironmentDelete {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $environmentName;
}}

if (!class_exists("EnvironmentDeleteResponse")) {
/**
 * EnvironmentDeleteResponse
 */
class EnvironmentDeleteResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $EnvironmentDeleteResult;
}}

if (!class_exists("EnvironmentGetColorProfiles")) {
/**
 * EnvironmentGetColorProfiles
 */
class EnvironmentGetColorProfiles {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
}}

if (!class_exists("EnvironmentGetColorProfilesResponse")) {
/**
 * EnvironmentGetColorProfilesResponse
 */
class EnvironmentGetColorProfilesResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $EnvironmentGetColorProfilesResult;
}}

if (!class_exists("EnvironmentGetCurrent")) {
/**
 * EnvironmentGetCurrent
 */
class EnvironmentGetCurrent {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
}}

if (!class_exists("EnvironmentGetCurrentResponse")) {
/**
 * EnvironmentGetCurrentResponse
 */
class EnvironmentGetCurrentResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $EnvironmentGetCurrentResult;
}}

if (!class_exists("EnvironmentGetDiskUsage")) {
/**
 * EnvironmentGetDiskUsage
 */
class EnvironmentGetDiskUsage {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $requestedResourceXML;
}}

if (!class_exists("EnvironmentGetDiskUsageResponse")) {
/**
 * EnvironmentGetDiskUsageResponse
 */
class EnvironmentGetDiskUsageResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $EnvironmentGetDiskUsageResult;
}}

if (!class_exists("EnvironmentGetLoginSettings")) {
/**
 * EnvironmentGetLoginSettings
 */
class EnvironmentGetLoginSettings {
	/**
	 * @access public
	 * @var sstring
	 */
	public $environmentNameOrURL;
}}

if (!class_exists("EnvironmentGetLoginSettingsResponse")) {
/**
 * EnvironmentGetLoginSettingsResponse
 */
class EnvironmentGetLoginSettingsResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $EnvironmentGetLoginSettingsResult;
}}

if (!class_exists("EnvironmentGetSettings")) {
/**
 * EnvironmentGetSettings
 */
class EnvironmentGetSettings {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $environmentName;
}}

if (!class_exists("EnvironmentGetSettingsResponse")) {
/**
 * EnvironmentGetSettingsResponse
 */
class EnvironmentGetSettingsResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $EnvironmentGetSettingsResult;
}}

if (!class_exists("EnvironmentList")) {
/**
 * EnvironmentList
 */
class EnvironmentList {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
}}

if (!class_exists("EnvironmentListResponse")) {
/**
 * EnvironmentListResponse
 */
class EnvironmentListResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $EnvironmentListResult;
}}

if (!class_exists("EnvironmentSaveSettings")) {
/**
 * EnvironmentSaveSettings
 */
class EnvironmentSaveSettings {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $environmentName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $xml;
}}

if (!class_exists("EnvironmentSaveSettingsResponse")) {
/**
 * EnvironmentSaveSettingsResponse
 */
class EnvironmentSaveSettingsResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $EnvironmentSaveSettingsResult;
}}

if (!class_exists("FontGetIncludedGlyphs")) {
/**
 * FontGetIncludedGlyphs
 */
class FontGetIncludedGlyphs {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $fontID;
}}

if (!class_exists("FontGetIncludedGlyphsResponse")) {
/**
 * FontGetIncludedGlyphsResponse
 */
class FontGetIncludedGlyphsResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $FontGetIncludedGlyphsResult;
}}

if (!class_exists("GenerateApiKey")) {
/**
 * GenerateApiKey
 */
class GenerateApiKey {
	/**
	 * @access public
	 * @var sstring
	 */
	public $environmentNameOrURL;
	/**
	 * @access public
	 * @var sstring
	 */
	public $userName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $password;
}}

if (!class_exists("GenerateApiKeyResponse")) {
/**
 * GenerateApiKeyResponse
 */
class GenerateApiKeyResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $GenerateApiKeyResult;
}}

if (!class_exists("GenerateApiKeyWithSettings")) {
/**
 * GenerateApiKeyWithSettings
 */
class GenerateApiKeyWithSettings {
	/**
	 * @access public
	 * @var sstring
	 */
	public $environmentNameOrURL;
	/**
	 * @access public
	 * @var sstring
	 */
	public $userName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $password;
	/**
	 * @access public
	 * @var sstring
	 */
	public $settingsXML;
}}

if (!class_exists("GenerateApiKeyWithSettingsResponse")) {
/**
 * GenerateApiKeyWithSettingsResponse
 */
class GenerateApiKeyWithSettingsResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $GenerateApiKeyWithSettingsResult;
}}

if (!class_exists("GetServerDate")) {
/**
 * GetServerDate
 */
class GetServerDate {
}}

if (!class_exists("GetServerDateResponse")) {
/**
 * GetServerDateResponse
 */
class GetServerDateResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $GetServerDateResult;
}}

if (!class_exists("HealthCheckExecute")) {
/**
 * HealthCheckExecute
 */
class HealthCheckExecute {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $itemID;
}}

if (!class_exists("HealthCheckExecuteResponse")) {
/**
 * HealthCheckExecuteResponse
 */
class HealthCheckExecuteResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $HealthCheckExecuteResult;
}}

if (!class_exists("IconSetAddIcon")) {
/**
 * IconSetAddIcon
 */
class IconSetAddIcon {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $itemID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $iconName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $pathOrData;
}}

if (!class_exists("IconSetAddIconResponse")) {
/**
 * IconSetAddIconResponse
 */
class IconSetAddIconResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $IconSetAddIconResult;
}}

if (!class_exists("IconSetDeleteIcon")) {
/**
 * IconSetDeleteIcon
 */
class IconSetDeleteIcon {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $itemID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $iconName;
}}

if (!class_exists("IconSetDeleteIconResponse")) {
/**
 * IconSetDeleteIconResponse
 */
class IconSetDeleteIconResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $IconSetDeleteIconResult;
}}

if (!class_exists("IconSetGetIcons")) {
/**
 * IconSetGetIcons
 */
class IconSetGetIcons {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $itemID;
	/**
	 * @access public
	 * @var sboolean
	 */
	public $allIcons;
}}

if (!class_exists("IconSetGetIconsResponse")) {
/**
 * IconSetGetIconsResponse
 */
class IconSetGetIconsResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $IconSetGetIconsResult;
}}

if (!class_exists("InterfaceGetInitialSettings")) {
/**
 * InterfaceGetInitialSettings
 */
class InterfaceGetInitialSettings {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
}}

if (!class_exists("InterfaceGetInitialSettingsResponse")) {
/**
 * InterfaceGetInitialSettingsResponse
 */
class InterfaceGetInitialSettingsResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $InterfaceGetInitialSettingsResult;
}}

if (!class_exists("LanguageGetCombinedStrings")) {
/**
 * LanguageGetCombinedStrings
 */
class LanguageGetCombinedStrings {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $languageID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $overrideBasedOn;
}}

if (!class_exists("LanguageGetCombinedStringsResponse")) {
/**
 * LanguageGetCombinedStringsResponse
 */
class LanguageGetCombinedStringsResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $LanguageGetCombinedStringsResult;
}}

if (!class_exists("LanguageGetCsvURL")) {
/**
 * LanguageGetCsvURL
 */
class LanguageGetCsvURL {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $languageID;
}}

if (!class_exists("LanguageGetCsvURLResponse")) {
/**
 * LanguageGetCsvURLResponse
 */
class LanguageGetCsvURLResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $LanguageGetCsvURLResult;
}}

if (!class_exists("LanguageGetUnicodeTextURL")) {
/**
 * LanguageGetUnicodeTextURL
 */
class LanguageGetUnicodeTextURL {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $languageID;
}}

if (!class_exists("LanguageGetUnicodeTextURLResponse")) {
/**
 * LanguageGetUnicodeTextURLResponse
 */
class LanguageGetUnicodeTextURLResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $LanguageGetUnicodeTextURLResult;
}}

if (!class_exists("LanguageImportCsv")) {
/**
 * LanguageImportCsv
 */
class LanguageImportCsv {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $languageID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $filePathOrData;
}}

if (!class_exists("LanguageImportCsvResponse")) {
/**
 * LanguageImportCsvResponse
 */
class LanguageImportCsvResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $LanguageImportCsvResult;
}}

if (!class_exists("LanguageImportUnicodeText")) {
/**
 * LanguageImportUnicodeText
 */
class LanguageImportUnicodeText {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $languageID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $filePathOrData;
}}

if (!class_exists("LanguageImportUnicodeTextResponse")) {
/**
 * LanguageImportUnicodeTextResponse
 */
class LanguageImportUnicodeTextResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $LanguageImportUnicodeTextResult;
}}

if (!class_exists("LanguageSaveStrings")) {
/**
 * LanguageSaveStrings
 */
class LanguageSaveStrings {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $languageID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $stringXML;
}}

if (!class_exists("LanguageSaveStringsResponse")) {
/**
 * LanguageSaveStringsResponse
 */
class LanguageSaveStringsResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $LanguageSaveStringsResult;
}}

if (!class_exists("LanguagesGetList")) {
/**
 * LanguagesGetList
 */
class LanguagesGetList {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sboolean
	 */
	public $includeSystemLanguages;
}}

if (!class_exists("LanguagesGetListResponse")) {
/**
 * LanguagesGetListResponse
 */
class LanguagesGetListResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $LanguagesGetListResult;
}}

if (!class_exists("MobileFeedGetDocumentList")) {
/**
 * MobileFeedGetDocumentList
 */
class MobileFeedGetDocumentList {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $feedID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $deviceInfoXML;
}}

if (!class_exists("MobileFeedGetDocumentListResponse")) {
/**
 * MobileFeedGetDocumentListResponse
 */
class MobileFeedGetDocumentListResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $MobileFeedGetDocumentListResult;
}}

if (!class_exists("MobileFeedGetDocumentXML")) {
/**
 * MobileFeedGetDocumentXML
 */
class MobileFeedGetDocumentXML {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $feedID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $deviceInfoXML;
	/**
	 * @access public
	 * @var sstring
	 */
	public $documentID;
}}

if (!class_exists("MobileFeedGetDocumentXMLResponse")) {
/**
 * MobileFeedGetDocumentXMLResponse
 */
class MobileFeedGetDocumentXMLResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $MobileFeedGetDocumentXMLResult;
}}

if (!class_exists("ProfilingClearSnapshot")) {
/**
 * ProfilingClearSnapshot
 */
class ProfilingClearSnapshot {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
}}

if (!class_exists("ProfilingClearSnapshotResponse")) {
/**
 * ProfilingClearSnapshotResponse
 */
class ProfilingClearSnapshotResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ProfilingClearSnapshotResult;
}}

if (!class_exists("ProfilingSaveSnapshot")) {
/**
 * ProfilingSaveSnapshot
 */
class ProfilingSaveSnapshot {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $fileName;
	/**
	 * @access public
	 * @var sboolean
	 */
	public $clear;
}}

if (!class_exists("ProfilingSaveSnapshotResponse")) {
/**
 * ProfilingSaveSnapshotResponse
 */
class ProfilingSaveSnapshotResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ProfilingSaveSnapshotResult;
}}

if (!class_exists("ResourceFolderAdd")) {
/**
 * ResourceFolderAdd
 */
class ResourceFolderAdd {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $resourceName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $newName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $parentPath;
}}

if (!class_exists("ResourceFolderAddResponse")) {
/**
 * ResourceFolderAddResponse
 */
class ResourceFolderAddResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ResourceFolderAddResult;
}}

if (!class_exists("ResourceFolderCopy")) {
/**
 * ResourceFolderCopy
 */
class ResourceFolderCopy {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $resourceName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $folderPath;
	/**
	 * @access public
	 * @var sstring
	 */
	public $newFolderPath;
	/**
	 * @access public
	 * @var sboolean
	 */
	public $includeSubFolders;
}}

if (!class_exists("ResourceFolderCopyResponse")) {
/**
 * ResourceFolderCopyResponse
 */
class ResourceFolderCopyResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ResourceFolderCopyResult;
}}

if (!class_exists("ResourceFolderDelete")) {
/**
 * ResourceFolderDelete
 */
class ResourceFolderDelete {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $resourceName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $relativePath;
}}

if (!class_exists("ResourceFolderDeleteResponse")) {
/**
 * ResourceFolderDeleteResponse
 */
class ResourceFolderDeleteResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ResourceFolderDeleteResult;
}}

if (!class_exists("ResourceFolderMove")) {
/**
 * ResourceFolderMove
 */
class ResourceFolderMove {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $resourceName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $folderPath;
	/**
	 * @access public
	 * @var sstring
	 */
	public $newFolderPath;
}}

if (!class_exists("ResourceFolderMoveResponse")) {
/**
 * ResourceFolderMoveResponse
 */
class ResourceFolderMoveResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ResourceFolderMoveResult;
}}

if (!class_exists("ResourceGetHistory")) {
/**
 * ResourceGetHistory
 */
class ResourceGetHistory {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $resourceName;
}}

if (!class_exists("ResourceGetHistoryResponse")) {
/**
 * ResourceGetHistoryResponse
 */
class ResourceGetHistoryResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ResourceGetHistoryResult;
}}

if (!class_exists("ResourceGetTree")) {
/**
 * ResourceGetTree
 */
class ResourceGetTree {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $resourceName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $parentFolder;
	/**
	 * @access public
	 * @var sboolean
	 */
	public $includeSubDirectories;
	/**
	 * @access public
	 * @var sboolean
	 */
	public $includeFiles;
}}

if (!class_exists("ResourceGetTreeResponse")) {
/**
 * ResourceGetTreeResponse
 */
class ResourceGetTreeResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ResourceGetTreeResult;
}}

if (!class_exists("ResourceGetTreeLevel")) {
/**
 * ResourceGetTreeLevel
 */
class ResourceGetTreeLevel {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $resourceName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $parentFolder;
	/**
	 * @access public
	 * @var sint
	 */
	public $numLevels;
}}

if (!class_exists("ResourceGetTreeLevelResponse")) {
/**
 * ResourceGetTreeLevelResponse
 */
class ResourceGetTreeLevelResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ResourceGetTreeLevelResult;
}}

if (!class_exists("ResourceItemAdd")) {
/**
 * ResourceItemAdd
 */
class ResourceItemAdd {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $resourceName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $newName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $folderPath;
	/**
	 * @access public
	 * @var sstring
	 */
	public $xml;
	/**
	 * @access public
	 * @var sstring
	 */
	public $fileData;
}}

if (!class_exists("ResourceItemAddResponse")) {
/**
 * ResourceItemAddResponse
 */
class ResourceItemAddResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ResourceItemAddResult;
}}

if (!class_exists("ResourceItemAddFromURL")) {
/**
 * ResourceItemAddFromURL
 */
class ResourceItemAddFromURL {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $resourceName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $newName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $folderPath;
	/**
	 * @access public
	 * @var sstring
	 */
	public $url;
	/**
	 * @access public
	 * @var sstring
	 */
	public $login;
	/**
	 * @access public
	 * @var sstring
	 */
	public $pw;
	/**
	 * @access public
	 * @var sboolean
	 */
	public $reuseExisting;
	/**
	 * @access public
	 * @var sstring
	 */
	public $previewFileURL;
	/**
	 * @access public
	 * @var sstring
	 */
	public $previewExtension;
	/**
	 * @access public
	 * @var sboolean
	 */
	public $isPermanentPreview;
}}

if (!class_exists("ResourceItemAddFromURLResponse")) {
/**
 * ResourceItemAddFromURLResponse
 */
class ResourceItemAddFromURLResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ResourceItemAddFromURLResult;
}}

if (!class_exists("ResourceItemAddFromURLWithModificationDate")) {
/**
 * ResourceItemAddFromURLWithModificationDate
 */
class ResourceItemAddFromURLWithModificationDate {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $resourceName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $newName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $folderPath;
	/**
	 * @access public
	 * @var sstring
	 */
	public $url;
	/**
	 * @access public
	 * @var sstring
	 */
	public $login;
	/**
	 * @access public
	 * @var sstring
	 */
	public $pw;
	/**
	 * @access public
	 * @var sboolean
	 */
	public $reuseExisting;
	/**
	 * @access public
	 * @var sstring
	 */
	public $previewFileURL;
	/**
	 * @access public
	 * @var sstring
	 */
	public $previewExtension;
	/**
	 * @access public
	 * @var sboolean
	 */
	public $isPermanentPreview;
	/**
	 * @access public
	 * @var sstring
	 */
	public $modificationDate;
}}

if (!class_exists("ResourceItemAddFromURLWithModificationDateResponse")) {
/**
 * ResourceItemAddFromURLWithModificationDateResponse
 */
class ResourceItemAddFromURLWithModificationDateResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ResourceItemAddFromURLWithModificationDateResult;
}}

if (!class_exists("ResourceItemAddPreviewOverride")) {
/**
 * ResourceItemAddPreviewOverride
 */
class ResourceItemAddPreviewOverride {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $resourceName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $itemID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $previewFileData;
	/**
	 * @access public
	 * @var sstring
	 */
	public $previewExtension;
	/**
	 * @access public
	 * @var sboolean
	 */
	public $isPermanentPreview;
}}

if (!class_exists("ResourceItemAddPreviewOverrideResponse")) {
/**
 * ResourceItemAddPreviewOverrideResponse
 */
class ResourceItemAddPreviewOverrideResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ResourceItemAddPreviewOverrideResult;
}}

if (!class_exists("ResourceItemAddWithPreview")) {
/**
 * ResourceItemAddWithPreview
 */
class ResourceItemAddWithPreview {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $resourceName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $newName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $folderPath;
	/**
	 * @access public
	 * @var sstring
	 */
	public $xml;
	/**
	 * @access public
	 * @var sstring
	 */
	public $fileData;
	/**
	 * @access public
	 * @var sstring
	 */
	public $previewFileData;
	/**
	 * @access public
	 * @var sstring
	 */
	public $previewExtension;
	/**
	 * @access public
	 * @var sboolean
	 */
	public $isPermanentPreview;
}}

if (!class_exists("ResourceItemAddWithPreviewResponse")) {
/**
 * ResourceItemAddWithPreviewResponse
 */
class ResourceItemAddWithPreviewResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ResourceItemAddWithPreviewResult;
}}

if (!class_exists("ResourceItemCopy")) {
/**
 * ResourceItemCopy
 */
class ResourceItemCopy {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $resourceName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $itemID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $newName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $folderPath;
}}

if (!class_exists("ResourceItemCopyResponse")) {
/**
 * ResourceItemCopyResponse
 */
class ResourceItemCopyResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ResourceItemCopyResult;
}}

if (!class_exists("ResourceItemDelete")) {
/**
 * ResourceItemDelete
 */
class ResourceItemDelete {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $resourceName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $itemID;
}}

if (!class_exists("ResourceItemDeleteResponse")) {
/**
 * ResourceItemDeleteResponse
 */
class ResourceItemDeleteResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ResourceItemDeleteResult;
}}

if (!class_exists("ResourceItemGetByIdOrPath")) {
/**
 * ResourceItemGetByIdOrPath
 */
class ResourceItemGetByIdOrPath {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $resourceName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $itemIdOrPath;
}}

if (!class_exists("ResourceItemGetByIdOrPathResponse")) {
/**
 * ResourceItemGetByIdOrPathResponse
 */
class ResourceItemGetByIdOrPathResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ResourceItemGetByIdOrPathResult;
}}

if (!class_exists("ResourceItemGetByName")) {
/**
 * ResourceItemGetByName
 */
class ResourceItemGetByName {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $resourceName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $itemName;
}}

if (!class_exists("ResourceItemGetByNameResponse")) {
/**
 * ResourceItemGetByNameResponse
 */
class ResourceItemGetByNameResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ResourceItemGetByNameResult;
}}

if (!class_exists("ResourceItemGetByPath")) {
/**
 * ResourceItemGetByPath
 */
class ResourceItemGetByPath {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $resourceName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $itemPath;
}}

if (!class_exists("ResourceItemGetByPathResponse")) {
/**
 * ResourceItemGetByPathResponse
 */
class ResourceItemGetByPathResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ResourceItemGetByPathResult;
}}

if (!class_exists("ResourceItemGetCacheInfo")) {
/**
 * ResourceItemGetCacheInfo
 */
class ResourceItemGetCacheInfo {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $resourceName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $itemID;
}}

if (!class_exists("ResourceItemGetCacheInfoResponse")) {
/**
 * ResourceItemGetCacheInfoResponse
 */
class ResourceItemGetCacheInfoResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ResourceItemGetCacheInfoResult;
}}

if (!class_exists("ResourceItemGetDefinitionXML")) {
/**
 * ResourceItemGetDefinitionXML
 */
class ResourceItemGetDefinitionXML {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $resourceName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $itemID;
}}

if (!class_exists("ResourceItemGetDefinitionXMLResponse")) {
/**
 * ResourceItemGetDefinitionXMLResponse
 */
class ResourceItemGetDefinitionXMLResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ResourceItemGetDefinitionXMLResult;
}}

if (!class_exists("ResourceItemGetHistory")) {
/**
 * ResourceItemGetHistory
 */
class ResourceItemGetHistory {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $resourceName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $itemID;
}}

if (!class_exists("ResourceItemGetHistoryResponse")) {
/**
 * ResourceItemGetHistoryResponse
 */
class ResourceItemGetHistoryResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ResourceItemGetHistoryResult;
}}

if (!class_exists("ResourceItemGetPrivateInfo")) {
/**
 * ResourceItemGetPrivateInfo
 */
class ResourceItemGetPrivateInfo {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $resourceName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $itemID;
}}

if (!class_exists("ResourceItemGetPrivateInfoResponse")) {
/**
 * ResourceItemGetPrivateInfoResponse
 */
class ResourceItemGetPrivateInfoResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ResourceItemGetPrivateInfoResult;
}}

if (!class_exists("ResourceItemGetTransformedURL")) {
/**
 * ResourceItemGetTransformedURL
 */
class ResourceItemGetTransformedURL {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $resourceName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $itemID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $type;
	/**
	 * @access public
	 * @var sstring
	 */
	public $transformationID;
	/**
	 * @access public
	 * @var sint
	 */
	public $pageNum;
}}

if (!class_exists("ResourceItemGetTransformedURLResponse")) {
/**
 * ResourceItemGetTransformedURLResponse
 */
class ResourceItemGetTransformedURLResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ResourceItemGetTransformedURLResult;
}}

if (!class_exists("ResourceItemGetTransformedURLWithDebugInfo")) {
/**
 * ResourceItemGetTransformedURLWithDebugInfo
 */
class ResourceItemGetTransformedURLWithDebugInfo {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $resourceName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $itemID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $type;
	/**
	 * @access public
	 * @var sstring
	 */
	public $transformationID;
	/**
	 * @access public
	 * @var sint
	 */
	public $pageNum;
}}

if (!class_exists("ResourceItemGetTransformedURLWithDebugInfoResponse")) {
/**
 * ResourceItemGetTransformedURLWithDebugInfoResponse
 */
class ResourceItemGetTransformedURLWithDebugInfoResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ResourceItemGetTransformedURLWithDebugInfoResult;
}}

if (!class_exists("ResourceItemGetURL")) {
/**
 * ResourceItemGetURL
 */
class ResourceItemGetURL {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $resourceName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $itemID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $type;
	/**
	 * @access public
	 * @var sint
	 */
	public $pageNum;
}}

if (!class_exists("ResourceItemGetURLResponse")) {
/**
 * ResourceItemGetURLResponse
 */
class ResourceItemGetURLResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ResourceItemGetURLResult;
}}

if (!class_exists("ResourceItemGetURLForAnonymousUser")) {
/**
 * ResourceItemGetURLForAnonymousUser
 */
class ResourceItemGetURLForAnonymousUser {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $resourceName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $itemID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $type;
	/**
	 * @access public
	 * @var sint
	 */
	public $pageNum;
}}

if (!class_exists("ResourceItemGetURLForAnonymousUserResponse")) {
/**
 * ResourceItemGetURLForAnonymousUserResponse
 */
class ResourceItemGetURLForAnonymousUserResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ResourceItemGetURLForAnonymousUserResult;
}}

if (!class_exists("ResourceItemGetURLWithDebugInfo")) {
/**
 * ResourceItemGetURLWithDebugInfo
 */
class ResourceItemGetURLWithDebugInfo {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $resourceName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $itemID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $type;
	/**
	 * @access public
	 * @var sint
	 */
	public $pageNum;
}}

if (!class_exists("ResourceItemGetURLWithDebugInfoResponse")) {
/**
 * ResourceItemGetURLWithDebugInfoResponse
 */
class ResourceItemGetURLWithDebugInfoResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ResourceItemGetURLWithDebugInfoResult;
}}

if (!class_exists("ResourceItemGetXML")) {
/**
 * ResourceItemGetXML
 */
class ResourceItemGetXML {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $resourceName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $itemID;
}}

if (!class_exists("ResourceItemGetXMLResponse")) {
/**
 * ResourceItemGetXMLResponse
 */
class ResourceItemGetXMLResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ResourceItemGetXMLResult;
}}

if (!class_exists("ResourceItemMove")) {
/**
 * ResourceItemMove
 */
class ResourceItemMove {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $resourceName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $itemID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $newName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $newFolderPath;
}}

if (!class_exists("ResourceItemMoveResponse")) {
/**
 * ResourceItemMoveResponse
 */
class ResourceItemMoveResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ResourceItemMoveResult;
}}

if (!class_exists("ResourceItemRemovePreviewOverride")) {
/**
 * ResourceItemRemovePreviewOverride
 */
class ResourceItemRemovePreviewOverride {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $resourceName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $itemID;
}}

if (!class_exists("ResourceItemRemovePreviewOverrideResponse")) {
/**
 * ResourceItemRemovePreviewOverrideResponse
 */
class ResourceItemRemovePreviewOverrideResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ResourceItemRemovePreviewOverrideResult;
}}

if (!class_exists("ResourceItemReplaceFile")) {
/**
 * ResourceItemReplaceFile
 */
class ResourceItemReplaceFile {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $resourceName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $itemID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $fileData;
}}

if (!class_exists("ResourceItemReplaceFileResponse")) {
/**
 * ResourceItemReplaceFileResponse
 */
class ResourceItemReplaceFileResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ResourceItemReplaceFileResult;
}}

if (!class_exists("ResourceItemReplaceFileWithPreviewOverride")) {
/**
 * ResourceItemReplaceFileWithPreviewOverride
 */
class ResourceItemReplaceFileWithPreviewOverride {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $resourceName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $itemID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $fileData;
	/**
	 * @access public
	 * @var sstring
	 */
	public $previewFileData;
	/**
	 * @access public
	 * @var sstring
	 */
	public $previewExtension;
	/**
	 * @access public
	 * @var sboolean
	 */
	public $isPermanentPreview;
}}

if (!class_exists("ResourceItemReplaceFileWithPreviewOverrideResponse")) {
/**
 * ResourceItemReplaceFileWithPreviewOverrideResponse
 */
class ResourceItemReplaceFileWithPreviewOverrideResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ResourceItemReplaceFileWithPreviewOverrideResult;
}}

if (!class_exists("ResourceItemResetPreviews")) {
/**
 * ResourceItemResetPreviews
 */
class ResourceItemResetPreviews {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $resourceName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $itemID;
}}

if (!class_exists("ResourceItemResetPreviewsResponse")) {
/**
 * ResourceItemResetPreviewsResponse
 */
class ResourceItemResetPreviewsResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ResourceItemResetPreviewsResult;
}}

if (!class_exists("ResourceItemsAddFromZip")) {
/**
 * ResourceItemsAddFromZip
 */
class ResourceItemsAddFromZip {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $resourceName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $folderPath;
	/**
	 * @access public
	 * @var sstring
	 */
	public $fileData;
}}

if (!class_exists("ResourceItemsAddFromZipResponse")) {
/**
 * ResourceItemsAddFromZipResponse
 */
class ResourceItemsAddFromZipResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ResourceItemsAddFromZipResult;
}}

if (!class_exists("ResourceItemSave")) {
/**
 * ResourceItemSave
 */
class ResourceItemSave {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $resourceName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $itemID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $xml;
}}

if (!class_exists("ResourceItemSaveResponse")) {
/**
 * ResourceItemSaveResponse
 */
class ResourceItemSaveResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ResourceItemSaveResult;
}}

if (!class_exists("ResourceLibraryGetSettings")) {
/**
 * ResourceLibraryGetSettings
 */
class ResourceLibraryGetSettings {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $resourceName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $libraryName;
}}

if (!class_exists("ResourceLibraryGetSettingsResponse")) {
/**
 * ResourceLibraryGetSettingsResponse
 */
class ResourceLibraryGetSettingsResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ResourceLibraryGetSettingsResult;
}}

if (!class_exists("ResourceLibrarySaveSettings")) {
/**
 * ResourceLibrarySaveSettings
 */
class ResourceLibrarySaveSettings {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $resourceName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $libraryName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $xml;
}}

if (!class_exists("ResourceLibrarySaveSettingsResponse")) {
/**
 * ResourceLibrarySaveSettingsResponse
 */
class ResourceLibrarySaveSettingsResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ResourceLibrarySaveSettingsResult;
}}

if (!class_exists("ResourceList")) {
/**
 * ResourceList
 */
class ResourceList {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
}}

if (!class_exists("ResourceListResponse")) {
/**
 * ResourceListResponse
 */
class ResourceListResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ResourceListResult;
}}

if (!class_exists("ResourceSearch")) {
/**
 * ResourceSearch
 */
class ResourceSearch {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $resourceName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $name;
}}

if (!class_exists("ResourceSearchResponse")) {
/**
 * ResourceSearchResponse
 */
class ResourceSearchResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ResourceSearchResult;
}}

if (!class_exists("ResourceSearchByIDs")) {
/**
 * ResourceSearchByIDs
 */
class ResourceSearchByIDs {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $resourceName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $IDs;
}}

if (!class_exists("ResourceSearchByIDsResponse")) {
/**
 * ResourceSearchByIDsResponse
 */
class ResourceSearchByIDsResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ResourceSearchByIDsResult;
}}

if (!class_exists("ResourceSearchInFolder")) {
/**
 * ResourceSearchInFolder
 */
class ResourceSearchInFolder {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $resourceName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $parentFolderPath;
	/**
	 * @access public
	 * @var sboolean
	 */
	public $includeSubDirectories;
	/**
	 * @access public
	 * @var sstring
	 */
	public $name;
}}

if (!class_exists("ResourceSearchInFolderResponse")) {
/**
 * ResourceSearchInFolderResponse
 */
class ResourceSearchInFolderResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ResourceSearchInFolderResult;
}}

if (!class_exists("ResourceSearchPaged")) {
/**
 * ResourceSearchPaged
 */
class ResourceSearchPaged {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $resourceName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $name;
	/**
	 * @access public
	 * @var sint
	 */
	public $pageSize;
	/**
	 * @access public
	 * @var sint
	 */
	public $pageNum;
}}

if (!class_exists("ResourceSearchPagedResponse")) {
/**
 * ResourceSearchPagedResponse
 */
class ResourceSearchPagedResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ResourceSearchPagedResult;
}}

if (!class_exists("ServerDeleteAllSaveSystemFileInfos")) {
/**
 * ServerDeleteAllSaveSystemFileInfos
 */
class ServerDeleteAllSaveSystemFileInfos {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
}}

if (!class_exists("ServerDeleteAllSaveSystemFileInfosResponse")) {
/**
 * ServerDeleteAllSaveSystemFileInfosResponse
 */
class ServerDeleteAllSaveSystemFileInfosResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ServerDeleteAllSaveSystemFileInfosResult;
}}

if (!class_exists("ServerDeleteSavedSystemInfoXML")) {
/**
 * ServerDeleteSavedSystemInfoXML
 */
class ServerDeleteSavedSystemInfoXML {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $name;
}}

if (!class_exists("ServerDeleteSavedSystemInfoXMLResponse")) {
/**
 * ServerDeleteSavedSystemInfoXMLResponse
 */
class ServerDeleteSavedSystemInfoXMLResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ServerDeleteSavedSystemInfoXMLResult;
}}

if (!class_exists("ServerGetLoggingSettings")) {
/**
 * ServerGetLoggingSettings
 */
class ServerGetLoggingSettings {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
}}

if (!class_exists("ServerGetLoggingSettingsResponse")) {
/**
 * ServerGetLoggingSettingsResponse
 */
class ServerGetLoggingSettingsResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ServerGetLoggingSettingsResult;
}}

if (!class_exists("ServerGetSavedSystemInfoList")) {
/**
 * ServerGetSavedSystemInfoList
 */
class ServerGetSavedSystemInfoList {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
}}

if (!class_exists("ServerGetSavedSystemInfoListResponse")) {
/**
 * ServerGetSavedSystemInfoListResponse
 */
class ServerGetSavedSystemInfoListResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ServerGetSavedSystemInfoListResult;
}}

if (!class_exists("ServerGetSavedSystemInfoXML")) {
/**
 * ServerGetSavedSystemInfoXML
 */
class ServerGetSavedSystemInfoXML {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $name;
}}

if (!class_exists("ServerGetSavedSystemInfoXMLResponse")) {
/**
 * ServerGetSavedSystemInfoXMLResponse
 */
class ServerGetSavedSystemInfoXMLResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ServerGetSavedSystemInfoXMLResult;
}}

if (!class_exists("ServerGetSettings")) {
/**
 * ServerGetSettings
 */
class ServerGetSettings {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
}}

if (!class_exists("ServerGetSettingsResponse")) {
/**
 * ServerGetSettingsResponse
 */
class ServerGetSettingsResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ServerGetSettingsResult;
}}

if (!class_exists("ServerGetSystemInfo")) {
/**
 * ServerGetSystemInfo
 */
class ServerGetSystemInfo {
	/**
	 * @access public
	 * @var sstring
	 */
	public $apiKey;
	/**
	 * @access public
	 * @var sstring
	 */
	public $data;
	/**
	 * @access public
	 * @var sboolean
	 */
	public $extended;
}}

if (!class_exists("ServerGetSystemInfoResponse")) {
/**
 * ServerGetSystemInfoResponse
 */
class ServerGetSystemInfoResponse {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ServerGetSystemInfoResult;
}}

if (!class_exists("main")) {
/**
 * main
 * @author WSDLInterpreter
 */
class main extends SoapClient {
	/**
	 * Default class map for wsdl=>php
	 * @access private
	 * @var array
	 */
	private static $classmap = array(
		"ServerLogClear" => "ServerLogClear",
		"ServerLogClearResponse" => "ServerLogClearResponse",
		"ServerSaveLoggingSettings" => "ServerSaveLoggingSettings",
		"ServerSaveLoggingSettingsResponse" => "ServerSaveLoggingSettingsResponse",
		"ServerSaveSettings" => "ServerSaveSettings",
		"ServerSaveSettingsResponse" => "ServerSaveSettingsResponse",
		"ServerSaveSystemInfoXML" => "ServerSaveSystemInfoXML",
		"ServerSaveSystemInfoXMLResponse" => "ServerSaveSystemInfoXMLResponse",
		"SetAssetDirectories" => "SetAssetDirectories",
		"SetAssetDirectoriesResponse" => "SetAssetDirectoriesResponse",
		"SetAutomaticPreviewGeneration" => "SetAutomaticPreviewGeneration",
		"SetAutomaticPreviewGenerationResponse" => "SetAutomaticPreviewGenerationResponse",
		"SetContentAdministration" => "SetContentAdministration",
		"SetContentAdministrationResponse" => "SetContentAdministrationResponse",
		"SetUserLanguage" => "SetUserLanguage",
		"SetUserLanguageResponse" => "SetUserLanguageResponse",
		"SetWorkingEnvironment" => "SetWorkingEnvironment",
		"SetWorkingEnvironmentResponse" => "SetWorkingEnvironmentResponse",
		"SetWorkspaceAdministration" => "SetWorkspaceAdministration",
		"SetWorkspaceAdministrationResponse" => "SetWorkspaceAdministrationResponse",
		"SpellCheckDictionariesGetSystemList" => "SpellCheckDictionariesGetSystemList",
		"SpellCheckDictionariesGetSystemListResponse" => "SpellCheckDictionariesGetSystemListResponse",
		"SpellCheckDictionaryAdd" => "SpellCheckDictionaryAdd",
		"SpellCheckDictionaryAddResponse" => "SpellCheckDictionaryAddResponse",
		"SpellCheckDictionaryAddFromSystem" => "SpellCheckDictionaryAddFromSystem",
		"SpellCheckDictionaryAddFromSystemResponse" => "SpellCheckDictionaryAddFromSystemResponse",
		"SpellCheckDictionaryReplaceFile" => "SpellCheckDictionaryReplaceFile",
		"SpellCheckDictionaryReplaceFileResponse" => "SpellCheckDictionaryReplaceFileResponse",
		"SwitchServerFlowGetCheckPoints" => "SwitchServerFlowGetCheckPoints",
		"SwitchServerFlowGetCheckPointsResponse" => "SwitchServerFlowGetCheckPointsResponse",
		"SwitchServerFlowGetElementsJobCount" => "SwitchServerFlowGetElementsJobCount",
		"SwitchServerFlowGetElementsJobCountResponse" => "SwitchServerFlowGetElementsJobCountResponse",
		"SwitchServerFlowGetFullConfig" => "SwitchServerFlowGetFullConfig",
		"SwitchServerFlowGetFullConfigResponse" => "SwitchServerFlowGetFullConfigResponse",
		"SwitchServerFlowGetJobs" => "SwitchServerFlowGetJobs",
		"SwitchServerFlowGetJobsResponse" => "SwitchServerFlowGetJobsResponse",
		"SwitchServerFlowGetSubmitPoints" => "SwitchServerFlowGetSubmitPoints",
		"SwitchServerFlowGetSubmitPointsResponse" => "SwitchServerFlowGetSubmitPointsResponse",
		"SwitchServerFlowSubmitFileToFolder" => "SwitchServerFlowSubmitFileToFolder",
		"SwitchServerFlowSubmitFileToFolderResponse" => "SwitchServerFlowSubmitFileToFolderResponse",
		"SwitchServerFlowSubmitFileToSubmitPoint" => "SwitchServerFlowSubmitFileToSubmitPoint",
		"SwitchServerFlowSubmitFileToSubmitPointResponse" => "SwitchServerFlowSubmitFileToSubmitPointResponse",
		"SwitchServerGetFlowList" => "SwitchServerGetFlowList",
		"SwitchServerGetFlowListResponse" => "SwitchServerGetFlowListResponse",
		"SwitchServerTestConnection" => "SwitchServerTestConnection",
		"SwitchServerTestConnectionResponse" => "SwitchServerTestConnectionResponse",
		"TaskGetEditorCliLog" => "TaskGetEditorCliLog",
		"TaskGetEditorCliLogResponse" => "TaskGetEditorCliLogResponse",
		"TaskGetStatus" => "TaskGetStatus",
		"TaskGetStatusResponse" => "TaskGetStatusResponse",
		"TaskGetStatusAndRemoveIfCompleted" => "TaskGetStatusAndRemoveIfCompleted",
		"TaskGetStatusAndRemoveIfCompletedResponse" => "TaskGetStatusAndRemoveIfCompletedResponse",
		"TaskRemoveFromLog" => "TaskRemoveFromLog",
		"TaskRemoveFromLogResponse" => "TaskRemoveFromLogResponse",
		"TasksGetList" => "TasksGetList",
		"TasksGetListResponse" => "TasksGetListResponse",
		"TasksGetQueueOverview" => "TasksGetQueueOverview",
		"TasksGetQueueOverviewResponse" => "TasksGetQueueOverviewResponse",
		"TasksGetStatusses" => "TasksGetStatusses",
		"TasksGetStatussesResponse" => "TasksGetStatussesResponse",
		"UploadExternalAsset" => "UploadExternalAsset",
		"UploadExternalAssetResponse" => "UploadExternalAssetResponse",
		"XinetExecutePortalDICall" => "XinetExecutePortalDICall",
		"XinetExecutePortalDICallResponse" => "XinetExecutePortalDICallResponse",
		"XinetSetCurrentCredentials" => "XinetSetCurrentCredentials",
		"XinetSetCurrentCredentialsResponse" => "XinetSetCurrentCredentialsResponse",
		"XinetTestConnection" => "XinetTestConnection",
		"XinetTestConnectionResponse" => "XinetTestConnectionResponse",
		"AdsGetFromURL" => "AdsGetFromURL",
		"AdsGetFromURLResponse" => "AdsGetFromURLResponse",
		"ApiKeyGetCurrentSettings" => "ApiKeyGetCurrentSettings",
		"ApiKeyGetCurrentSettingsResponse" => "ApiKeyGetCurrentSettingsResponse",
		"ApiKeyKeepAlive" => "ApiKeyKeepAlive",
		"ApiKeyKeepAliveResponse" => "ApiKeyKeepAliveResponse",
		"AssetGetImageInfo" => "AssetGetImageInfo",
		"AssetGetImageInfoResponse" => "AssetGetImageInfoResponse",
		"BarcodeCreate" => "BarcodeCreate",
		"BarcodeCreateResponse" => "BarcodeCreateResponse",
		"BarcodeCreateColored" => "BarcodeCreateColored",
		"BarcodeCreateColoredResponse" => "BarcodeCreateColoredResponse",
		"CsvFileCreate" => "CsvFileCreate",
		"CsvFileCreateResponse" => "CsvFileCreateResponse",
		"DataSourceAddSampleFile" => "DataSourceAddSampleFile",
		"DataSourceAddSampleFileResponse" => "DataSourceAddSampleFileResponse",
		"DataSourceDeleteSampleFile" => "DataSourceDeleteSampleFile",
		"DataSourceDeleteSampleFileResponse" => "DataSourceDeleteSampleFileResponse",
		"DataSourceDownloadSpreadsheets" => "DataSourceDownloadSpreadsheets",
		"DataSourceDownloadSpreadsheetsResponse" => "DataSourceDownloadSpreadsheetsResponse",
		"DataSourceDownloadURL" => "DataSourceDownloadURL",
		"DataSourceDownloadURLResponse" => "DataSourceDownloadURLResponse",
		"DataSourceFileGetXML" => "DataSourceFileGetXML",
		"DataSourceFileGetXMLResponse" => "DataSourceFileGetXMLResponse",
		"DataSourceListSampleFiles" => "DataSourceListSampleFiles",
		"DataSourceListSampleFilesResponse" => "DataSourceListSampleFilesResponse",
		"DataSourceSalesForceGetXML" => "DataSourceSalesForceGetXML",
		"DataSourceSalesForceGetXMLResponse" => "DataSourceSalesForceGetXMLResponse",
		"DataSourceSpreadsheetGetXML" => "DataSourceSpreadsheetGetXML",
		"DataSourceSpreadsheetGetXMLResponse" => "DataSourceSpreadsheetGetXMLResponse",
		"DocumentCopyAnnotations" => "DocumentCopyAnnotations",
		"DocumentCopyAnnotationsResponse" => "DocumentCopyAnnotationsResponse",
		"DocumentCopyDocumentEventActions" => "DocumentCopyDocumentEventActions",
		"DocumentCopyDocumentEventActionsResponse" => "DocumentCopyDocumentEventActionsResponse",
		"DocumentCopyVariableDefinitions" => "DocumentCopyVariableDefinitions",
		"DocumentCopyVariableDefinitionsResponse" => "DocumentCopyVariableDefinitionsResponse",
		"DocumentCreateFromBlankDocTemplate" => "DocumentCreateFromBlankDocTemplate",
		"DocumentCreateFromBlankDocTemplateResponse" => "DocumentCreateFromBlankDocTemplateResponse",
		"DocumentCreateFromChiliPackage" => "DocumentCreateFromChiliPackage",
		"DocumentCreateFromChiliPackageResponse" => "DocumentCreateFromChiliPackageResponse",
		"DocumentCreateFromPDF" => "DocumentCreateFromPDF",
		"DocumentCreateFromPDFResponse" => "DocumentCreateFromPDFResponse",
		"DocumentCreateImages" => "DocumentCreateImages",
		"DocumentCreateImagesResponse" => "DocumentCreateImagesResponse",
		"DocumentCreateImagesAndPDF" => "DocumentCreateImagesAndPDF",
		"DocumentCreateImagesAndPDFResponse" => "DocumentCreateImagesAndPDFResponse",
		"DocumentCreatePackage" => "DocumentCreatePackage",
		"DocumentCreatePackageResponse" => "DocumentCreatePackageResponse",
		"DocumentCreatePDF" => "DocumentCreatePDF",
		"DocumentCreatePDFResponse" => "DocumentCreatePDFResponse",
		"DocumentCreateTempFolding" => "DocumentCreateTempFolding",
		"DocumentCreateTempFoldingResponse" => "DocumentCreateTempFoldingResponse",
		"DocumentCreateTempImages" => "DocumentCreateTempImages",
		"DocumentCreateTempImagesResponse" => "DocumentCreateTempImagesResponse",
		"DocumentCreateTempImagesAndPDF" => "DocumentCreateTempImagesAndPDF",
		"DocumentCreateTempImagesAndPDFResponse" => "DocumentCreateTempImagesAndPDFResponse",
		"DocumentCreateTempPackage" => "DocumentCreateTempPackage",
		"DocumentCreateTempPackageResponse" => "DocumentCreateTempPackageResponse",
		"DocumentCreateTempPDF" => "DocumentCreateTempPDF",
		"DocumentCreateTempPDFResponse" => "DocumentCreateTempPDFResponse",
		"DocumentGetAnnotations" => "DocumentGetAnnotations",
		"DocumentGetAnnotationsResponse" => "DocumentGetAnnotationsResponse",
		"DocumentGetDefaultSettings" => "DocumentGetDefaultSettings",
		"DocumentGetDefaultSettingsResponse" => "DocumentGetDefaultSettingsResponse",
		"DocumentGetDocumentEventActions" => "DocumentGetDocumentEventActions",
		"DocumentGetDocumentEventActionsResponse" => "DocumentGetDocumentEventActionsResponse",
		"DocumentGetEditorURL" => "DocumentGetEditorURL",
		"DocumentGetEditorURLResponse" => "DocumentGetEditorURLResponse",
		"DocumentGetFoldingViewerURL" => "DocumentGetFoldingViewerURL",
		"DocumentGetFoldingViewerURLResponse" => "DocumentGetFoldingViewerURLResponse",
		"DocumentGetInfo" => "DocumentGetInfo",
		"DocumentGetInfoResponse" => "DocumentGetInfoResponse",
		"DocumentGetIpadXML" => "DocumentGetIpadXML",
		"DocumentGetIpadXMLResponse" => "DocumentGetIpadXMLResponse",
		"DocumentGetPlacedAdsAndEdit" => "DocumentGetPlacedAdsAndEdit",
		"DocumentGetPlacedAdsAndEditResponse" => "DocumentGetPlacedAdsAndEditResponse",
		"DocumentGetPreflightResults" => "DocumentGetPreflightResults",
		"DocumentGetPreflightResultsResponse" => "DocumentGetPreflightResultsResponse",
		"DocumentGetUsedAssets" => "DocumentGetUsedAssets",
		"DocumentGetUsedAssetsResponse" => "DocumentGetUsedAssetsResponse",
		"DocumentGetVariableDefinitions" => "DocumentGetVariableDefinitions",
		"DocumentGetVariableDefinitionsResponse" => "DocumentGetVariableDefinitionsResponse",
		"DocumentGetVariableValues" => "DocumentGetVariableValues",
		"DocumentGetVariableValuesResponse" => "DocumentGetVariableValuesResponse",
		"DocumentSetAnnotations" => "DocumentSetAnnotations",
		"DocumentSetAnnotationsResponse" => "DocumentSetAnnotationsResponse",
		"DocumentSetAssetDirectories" => "DocumentSetAssetDirectories",
		"DocumentSetAssetDirectoriesResponse" => "DocumentSetAssetDirectoriesResponse",
		"DocumentSetConstraints" => "DocumentSetConstraints",
		"DocumentSetConstraintsResponse" => "DocumentSetConstraintsResponse",
		"DocumentSetDocumentEventActions" => "DocumentSetDocumentEventActions",
		"DocumentSetDocumentEventActionsResponse" => "DocumentSetDocumentEventActionsResponse",
		"DocumentSetVariableDefinitions" => "DocumentSetVariableDefinitions",
		"DocumentSetVariableDefinitionsResponse" => "DocumentSetVariableDefinitionsResponse",
		"DocumentSetVariableValues" => "DocumentSetVariableValues",
		"DocumentSetVariableValuesResponse" => "DocumentSetVariableValuesResponse",
		"DownloadURL" => "DownloadURL",
		"DownloadURLResponse" => "DownloadURLResponse",
		"EditsGetFromURL" => "EditsGetFromURL",
		"EditsGetFromURLResponse" => "EditsGetFromURLResponse",
		"EnvironmentAdd" => "EnvironmentAdd",
		"EnvironmentAddResponse" => "EnvironmentAddResponse",
		"EnvironmentCopy" => "EnvironmentCopy",
		"EnvironmentCopyResponse" => "EnvironmentCopyResponse",
		"EnvironmentDelete" => "EnvironmentDelete",
		"EnvironmentDeleteResponse" => "EnvironmentDeleteResponse",
		"EnvironmentGetColorProfiles" => "EnvironmentGetColorProfiles",
		"EnvironmentGetColorProfilesResponse" => "EnvironmentGetColorProfilesResponse",
		"EnvironmentGetCurrent" => "EnvironmentGetCurrent",
		"EnvironmentGetCurrentResponse" => "EnvironmentGetCurrentResponse",
		"EnvironmentGetDiskUsage" => "EnvironmentGetDiskUsage",
		"EnvironmentGetDiskUsageResponse" => "EnvironmentGetDiskUsageResponse",
		"EnvironmentGetLoginSettings" => "EnvironmentGetLoginSettings",
		"EnvironmentGetLoginSettingsResponse" => "EnvironmentGetLoginSettingsResponse",
		"EnvironmentGetSettings" => "EnvironmentGetSettings",
		"EnvironmentGetSettingsResponse" => "EnvironmentGetSettingsResponse",
		"EnvironmentList" => "EnvironmentList",
		"EnvironmentListResponse" => "EnvironmentListResponse",
		"EnvironmentSaveSettings" => "EnvironmentSaveSettings",
		"EnvironmentSaveSettingsResponse" => "EnvironmentSaveSettingsResponse",
		"FontGetIncludedGlyphs" => "FontGetIncludedGlyphs",
		"FontGetIncludedGlyphsResponse" => "FontGetIncludedGlyphsResponse",
		"GenerateApiKey" => "GenerateApiKey",
		"GenerateApiKeyResponse" => "GenerateApiKeyResponse",
		"GenerateApiKeyWithSettings" => "GenerateApiKeyWithSettings",
		"GenerateApiKeyWithSettingsResponse" => "GenerateApiKeyWithSettingsResponse",
		"GetServerDate" => "GetServerDate",
		"GetServerDateResponse" => "GetServerDateResponse",
		"HealthCheckExecute" => "HealthCheckExecute",
		"HealthCheckExecuteResponse" => "HealthCheckExecuteResponse",
		"IconSetAddIcon" => "IconSetAddIcon",
		"IconSetAddIconResponse" => "IconSetAddIconResponse",
		"IconSetDeleteIcon" => "IconSetDeleteIcon",
		"IconSetDeleteIconResponse" => "IconSetDeleteIconResponse",
		"IconSetGetIcons" => "IconSetGetIcons",
		"IconSetGetIconsResponse" => "IconSetGetIconsResponse",
		"InterfaceGetInitialSettings" => "InterfaceGetInitialSettings",
		"InterfaceGetInitialSettingsResponse" => "InterfaceGetInitialSettingsResponse",
		"LanguageGetCombinedStrings" => "LanguageGetCombinedStrings",
		"LanguageGetCombinedStringsResponse" => "LanguageGetCombinedStringsResponse",
		"LanguageGetCsvURL" => "LanguageGetCsvURL",
		"LanguageGetCsvURLResponse" => "LanguageGetCsvURLResponse",
		"LanguageGetUnicodeTextURL" => "LanguageGetUnicodeTextURL",
		"LanguageGetUnicodeTextURLResponse" => "LanguageGetUnicodeTextURLResponse",
		"LanguageImportCsv" => "LanguageImportCsv",
		"LanguageImportCsvResponse" => "LanguageImportCsvResponse",
		"LanguageImportUnicodeText" => "LanguageImportUnicodeText",
		"LanguageImportUnicodeTextResponse" => "LanguageImportUnicodeTextResponse",
		"LanguageSaveStrings" => "LanguageSaveStrings",
		"LanguageSaveStringsResponse" => "LanguageSaveStringsResponse",
		"LanguagesGetList" => "LanguagesGetList",
		"LanguagesGetListResponse" => "LanguagesGetListResponse",
		"MobileFeedGetDocumentList" => "MobileFeedGetDocumentList",
		"MobileFeedGetDocumentListResponse" => "MobileFeedGetDocumentListResponse",
		"MobileFeedGetDocumentXML" => "MobileFeedGetDocumentXML",
		"MobileFeedGetDocumentXMLResponse" => "MobileFeedGetDocumentXMLResponse",
		"ProfilingClearSnapshot" => "ProfilingClearSnapshot",
		"ProfilingClearSnapshotResponse" => "ProfilingClearSnapshotResponse",
		"ProfilingSaveSnapshot" => "ProfilingSaveSnapshot",
		"ProfilingSaveSnapshotResponse" => "ProfilingSaveSnapshotResponse",
		"ResourceFolderAdd" => "ResourceFolderAdd",
		"ResourceFolderAddResponse" => "ResourceFolderAddResponse",
		"ResourceFolderCopy" => "ResourceFolderCopy",
		"ResourceFolderCopyResponse" => "ResourceFolderCopyResponse",
		"ResourceFolderDelete" => "ResourceFolderDelete",
		"ResourceFolderDeleteResponse" => "ResourceFolderDeleteResponse",
		"ResourceFolderMove" => "ResourceFolderMove",
		"ResourceFolderMoveResponse" => "ResourceFolderMoveResponse",
		"ResourceGetHistory" => "ResourceGetHistory",
		"ResourceGetHistoryResponse" => "ResourceGetHistoryResponse",
		"ResourceGetTree" => "ResourceGetTree",
		"ResourceGetTreeResponse" => "ResourceGetTreeResponse",
		"ResourceGetTreeLevel" => "ResourceGetTreeLevel",
		"ResourceGetTreeLevelResponse" => "ResourceGetTreeLevelResponse",
		"ResourceItemAdd" => "ResourceItemAdd",
		"ResourceItemAddResponse" => "ResourceItemAddResponse",
		"ResourceItemAddFromURL" => "ResourceItemAddFromURL",
		"ResourceItemAddFromURLResponse" => "ResourceItemAddFromURLResponse",
		"ResourceItemAddFromURLWithModificationDate" => "ResourceItemAddFromURLWithModificationDate",
		"ResourceItemAddFromURLWithModificationDateResponse" => "ResourceItemAddFromURLWithModificationDateResponse",
		"ResourceItemAddPreviewOverride" => "ResourceItemAddPreviewOverride",
		"ResourceItemAddPreviewOverrideResponse" => "ResourceItemAddPreviewOverrideResponse",
		"ResourceItemAddWithPreview" => "ResourceItemAddWithPreview",
		"ResourceItemAddWithPreviewResponse" => "ResourceItemAddWithPreviewResponse",
		"ResourceItemCopy" => "ResourceItemCopy",
		"ResourceItemCopyResponse" => "ResourceItemCopyResponse",
		"ResourceItemDelete" => "ResourceItemDelete",
		"ResourceItemDeleteResponse" => "ResourceItemDeleteResponse",
		"ResourceItemGetByIdOrPath" => "ResourceItemGetByIdOrPath",
		"ResourceItemGetByIdOrPathResponse" => "ResourceItemGetByIdOrPathResponse",
		"ResourceItemGetByName" => "ResourceItemGetByName",
		"ResourceItemGetByNameResponse" => "ResourceItemGetByNameResponse",
		"ResourceItemGetByPath" => "ResourceItemGetByPath",
		"ResourceItemGetByPathResponse" => "ResourceItemGetByPathResponse",
		"ResourceItemGetCacheInfo" => "ResourceItemGetCacheInfo",
		"ResourceItemGetCacheInfoResponse" => "ResourceItemGetCacheInfoResponse",
		"ResourceItemGetDefinitionXML" => "ResourceItemGetDefinitionXML",
		"ResourceItemGetDefinitionXMLResponse" => "ResourceItemGetDefinitionXMLResponse",
		"ResourceItemGetHistory" => "ResourceItemGetHistory",
		"ResourceItemGetHistoryResponse" => "ResourceItemGetHistoryResponse",
		"ResourceItemGetPrivateInfo" => "ResourceItemGetPrivateInfo",
		"ResourceItemGetPrivateInfoResponse" => "ResourceItemGetPrivateInfoResponse",
		"ResourceItemGetTransformedURL" => "ResourceItemGetTransformedURL",
		"ResourceItemGetTransformedURLResponse" => "ResourceItemGetTransformedURLResponse",
		"ResourceItemGetTransformedURLWithDebugInfo" => "ResourceItemGetTransformedURLWithDebugInfo",
		"ResourceItemGetTransformedURLWithDebugInfoResponse" => "ResourceItemGetTransformedURLWithDebugInfoResponse",
		"ResourceItemGetURL" => "ResourceItemGetURL",
		"ResourceItemGetURLResponse" => "ResourceItemGetURLResponse",
		"ResourceItemGetURLForAnonymousUser" => "ResourceItemGetURLForAnonymousUser",
		"ResourceItemGetURLForAnonymousUserResponse" => "ResourceItemGetURLForAnonymousUserResponse",
		"ResourceItemGetURLWithDebugInfo" => "ResourceItemGetURLWithDebugInfo",
		"ResourceItemGetURLWithDebugInfoResponse" => "ResourceItemGetURLWithDebugInfoResponse",
		"ResourceItemGetXML" => "ResourceItemGetXML",
		"ResourceItemGetXMLResponse" => "ResourceItemGetXMLResponse",
		"ResourceItemMove" => "ResourceItemMove",
		"ResourceItemMoveResponse" => "ResourceItemMoveResponse",
		"ResourceItemRemovePreviewOverride" => "ResourceItemRemovePreviewOverride",
		"ResourceItemRemovePreviewOverrideResponse" => "ResourceItemRemovePreviewOverrideResponse",
		"ResourceItemReplaceFile" => "ResourceItemReplaceFile",
		"ResourceItemReplaceFileResponse" => "ResourceItemReplaceFileResponse",
		"ResourceItemReplaceFileWithPreviewOverride" => "ResourceItemReplaceFileWithPreviewOverride",
		"ResourceItemReplaceFileWithPreviewOverrideResponse" => "ResourceItemReplaceFileWithPreviewOverrideResponse",
		"ResourceItemResetPreviews" => "ResourceItemResetPreviews",
		"ResourceItemResetPreviewsResponse" => "ResourceItemResetPreviewsResponse",
		"ResourceItemsAddFromZip" => "ResourceItemsAddFromZip",
		"ResourceItemsAddFromZipResponse" => "ResourceItemsAddFromZipResponse",
		"ResourceItemSave" => "ResourceItemSave",
		"ResourceItemSaveResponse" => "ResourceItemSaveResponse",
		"ResourceLibraryGetSettings" => "ResourceLibraryGetSettings",
		"ResourceLibraryGetSettingsResponse" => "ResourceLibraryGetSettingsResponse",
		"ResourceLibrarySaveSettings" => "ResourceLibrarySaveSettings",
		"ResourceLibrarySaveSettingsResponse" => "ResourceLibrarySaveSettingsResponse",
		"ResourceList" => "ResourceList",
		"ResourceListResponse" => "ResourceListResponse",
		"ResourceSearch" => "ResourceSearch",
		"ResourceSearchResponse" => "ResourceSearchResponse",
		"ResourceSearchByIDs" => "ResourceSearchByIDs",
		"ResourceSearchByIDsResponse" => "ResourceSearchByIDsResponse",
		"ResourceSearchInFolder" => "ResourceSearchInFolder",
		"ResourceSearchInFolderResponse" => "ResourceSearchInFolderResponse",
		"ResourceSearchPaged" => "ResourceSearchPaged",
		"ResourceSearchPagedResponse" => "ResourceSearchPagedResponse",
		"ServerDeleteAllSaveSystemFileInfos" => "ServerDeleteAllSaveSystemFileInfos",
		"ServerDeleteAllSaveSystemFileInfosResponse" => "ServerDeleteAllSaveSystemFileInfosResponse",
		"ServerDeleteSavedSystemInfoXML" => "ServerDeleteSavedSystemInfoXML",
		"ServerDeleteSavedSystemInfoXMLResponse" => "ServerDeleteSavedSystemInfoXMLResponse",
		"ServerGetLoggingSettings" => "ServerGetLoggingSettings",
		"ServerGetLoggingSettingsResponse" => "ServerGetLoggingSettingsResponse",
		"ServerGetSavedSystemInfoList" => "ServerGetSavedSystemInfoList",
		"ServerGetSavedSystemInfoListResponse" => "ServerGetSavedSystemInfoListResponse",
		"ServerGetSavedSystemInfoXML" => "ServerGetSavedSystemInfoXML",
		"ServerGetSavedSystemInfoXMLResponse" => "ServerGetSavedSystemInfoXMLResponse",
		"ServerGetSettings" => "ServerGetSettings",
		"ServerGetSettingsResponse" => "ServerGetSettingsResponse",
		"ServerGetSystemInfo" => "ServerGetSystemInfo",
		"ServerGetSystemInfoResponse" => "ServerGetSystemInfoResponse",
	);

	/**
	 * Constructor using wsdl location and options array
	 * @param string $wsdl WSDL location for this service
	 * @param array $options Options for the SoapClient
	 */
	public function __construct($wsdl="http://dev2.chili-publish.com/BO/Main.asmx?WSDL", $options=array()) {
		foreach(self::$classmap as $wsdlClassName => $phpClassName) {
		    if(!isset($options['classmap'][$wsdlClassName])) {
		        $options['classmap'][$wsdlClassName] = $phpClassName;
		    }
		}
		parent::__construct($wsdl, $options);
	}

	/**
	 * Checks if an argument list matches against a valid argument type list
	 * @param array $arguments The argument list to check
	 * @param array $validParameters A list of valid argument types
	 * @return boolean true if arguments match against validParameters
	 * @throws Exception invalid function signature message
	 */
	public function _checkArguments($arguments, $validParameters) {
		$variables = "";
		foreach ($arguments as $arg) {
		    $type = gettype($arg);
		    if ($type == "object") {
		        $type = get_class($arg);
		    }
		    $variables .= "(".$type.")";
		}
		if (!in_array($variables, $validParameters)) {
		    throw new Exception("Invalid parameter types: ".str_replace(")(", ", ", $variables));
		}
		return true;
	}

	/**
	 * Service Call: ServerLogClear
	 * Parameter options:
	 * (ServerLogClear) parameters
	 * (ServerLogClear) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ServerLogClearResponse
	 * @throws Exception invalid function signature message
	 */
	public function ServerLogClear($mixed = null) {
		$validParameters = array(
			"(ServerLogClear)",
			"(ServerLogClear)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ServerLogClear", $args);
	}


	/**
	 * Service Call: ServerSaveLoggingSettings
	 * Parameter options:
	 * (ServerSaveLoggingSettings) parameters
	 * (ServerSaveLoggingSettings) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ServerSaveLoggingSettingsResponse
	 * @throws Exception invalid function signature message
	 */
	public function ServerSaveLoggingSettings($mixed = null) {
		$validParameters = array(
			"(ServerSaveLoggingSettings)",
			"(ServerSaveLoggingSettings)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ServerSaveLoggingSettings", $args);
	}


	/**
	 * Service Call: ServerSaveSettings
	 * Parameter options:
	 * (ServerSaveSettings) parameters
	 * (ServerSaveSettings) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ServerSaveSettingsResponse
	 * @throws Exception invalid function signature message
	 */
	public function ServerSaveSettings($mixed = null) {
		$validParameters = array(
			"(ServerSaveSettings)",
			"(ServerSaveSettings)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ServerSaveSettings", $args);
	}


	/**
	 * Service Call: ServerSaveSystemInfoXML
	 * Parameter options:
	 * (ServerSaveSystemInfoXML) parameters
	 * (ServerSaveSystemInfoXML) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ServerSaveSystemInfoXMLResponse
	 * @throws Exception invalid function signature message
	 */
	public function ServerSaveSystemInfoXML($mixed = null) {
		$validParameters = array(
			"(ServerSaveSystemInfoXML)",
			"(ServerSaveSystemInfoXML)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ServerSaveSystemInfoXML", $args);
	}


	/**
	 * Service Call: SetAssetDirectories
	 * Parameter options:
	 * (SetAssetDirectories) parameters
	 * (SetAssetDirectories) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetAssetDirectoriesResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetAssetDirectories($mixed = null) {
		$validParameters = array(
			"(SetAssetDirectories)",
			"(SetAssetDirectories)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetAssetDirectories", $args);
	}


	/**
	 * Service Call: SetAutomaticPreviewGeneration
	 * Parameter options:
	 * (SetAutomaticPreviewGeneration) parameters
	 * (SetAutomaticPreviewGeneration) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetAutomaticPreviewGenerationResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetAutomaticPreviewGeneration($mixed = null) {
		$validParameters = array(
			"(SetAutomaticPreviewGeneration)",
			"(SetAutomaticPreviewGeneration)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetAutomaticPreviewGeneration", $args);
	}


	/**
	 * Service Call: SetContentAdministration
	 * Parameter options:
	 * (SetContentAdministration) parameters
	 * (SetContentAdministration) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetContentAdministrationResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetContentAdministration($mixed = null) {
		$validParameters = array(
			"(SetContentAdministration)",
			"(SetContentAdministration)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetContentAdministration", $args);
	}


	/**
	 * Service Call: SetUserLanguage
	 * Parameter options:
	 * (SetUserLanguage) parameters
	 * (SetUserLanguage) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetUserLanguageResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetUserLanguage($mixed = null) {
		$validParameters = array(
			"(SetUserLanguage)",
			"(SetUserLanguage)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetUserLanguage", $args);
	}


	/**
	 * Service Call: SetWorkingEnvironment
	 * Parameter options:
	 * (SetWorkingEnvironment) parameters
	 * (SetWorkingEnvironment) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetWorkingEnvironmentResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetWorkingEnvironment($mixed = null) {
		$validParameters = array(
			"(SetWorkingEnvironment)",
			"(SetWorkingEnvironment)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetWorkingEnvironment", $args);
	}


	/**
	 * Service Call: SetWorkspaceAdministration
	 * Parameter options:
	 * (SetWorkspaceAdministration) parameters
	 * (SetWorkspaceAdministration) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetWorkspaceAdministrationResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetWorkspaceAdministration($mixed = null) {
		$validParameters = array(
			"(SetWorkspaceAdministration)",
			"(SetWorkspaceAdministration)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetWorkspaceAdministration", $args);
	}


	/**
	 * Service Call: SpellCheckDictionariesGetSystemList
	 * Parameter options:
	 * (SpellCheckDictionariesGetSystemList) parameters
	 * (SpellCheckDictionariesGetSystemList) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SpellCheckDictionariesGetSystemListResponse
	 * @throws Exception invalid function signature message
	 */
	public function SpellCheckDictionariesGetSystemList($mixed = null) {
		$validParameters = array(
			"(SpellCheckDictionariesGetSystemList)",
			"(SpellCheckDictionariesGetSystemList)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SpellCheckDictionariesGetSystemList", $args);
	}


	/**
	 * Service Call: SpellCheckDictionaryAdd
	 * Parameter options:
	 * (SpellCheckDictionaryAdd) parameters
	 * (SpellCheckDictionaryAdd) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SpellCheckDictionaryAddResponse
	 * @throws Exception invalid function signature message
	 */
	public function SpellCheckDictionaryAdd($mixed = null) {
		$validParameters = array(
			"(SpellCheckDictionaryAdd)",
			"(SpellCheckDictionaryAdd)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SpellCheckDictionaryAdd", $args);
	}


	/**
	 * Service Call: SpellCheckDictionaryAddFromSystem
	 * Parameter options:
	 * (SpellCheckDictionaryAddFromSystem) parameters
	 * (SpellCheckDictionaryAddFromSystem) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SpellCheckDictionaryAddFromSystemResponse
	 * @throws Exception invalid function signature message
	 */
	public function SpellCheckDictionaryAddFromSystem($mixed = null) {
		$validParameters = array(
			"(SpellCheckDictionaryAddFromSystem)",
			"(SpellCheckDictionaryAddFromSystem)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SpellCheckDictionaryAddFromSystem", $args);
	}


	/**
	 * Service Call: SpellCheckDictionaryReplaceFile
	 * Parameter options:
	 * (SpellCheckDictionaryReplaceFile) parameters
	 * (SpellCheckDictionaryReplaceFile) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SpellCheckDictionaryReplaceFileResponse
	 * @throws Exception invalid function signature message
	 */
	public function SpellCheckDictionaryReplaceFile($mixed = null) {
		$validParameters = array(
			"(SpellCheckDictionaryReplaceFile)",
			"(SpellCheckDictionaryReplaceFile)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SpellCheckDictionaryReplaceFile", $args);
	}


	/**
	 * Service Call: SwitchServerFlowGetCheckPoints
	 * Parameter options:
	 * (SwitchServerFlowGetCheckPoints) parameters
	 * (SwitchServerFlowGetCheckPoints) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SwitchServerFlowGetCheckPointsResponse
	 * @throws Exception invalid function signature message
	 */
	public function SwitchServerFlowGetCheckPoints($mixed = null) {
		$validParameters = array(
			"(SwitchServerFlowGetCheckPoints)",
			"(SwitchServerFlowGetCheckPoints)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SwitchServerFlowGetCheckPoints", $args);
	}


	/**
	 * Service Call: SwitchServerFlowGetElementsJobCount
	 * Parameter options:
	 * (SwitchServerFlowGetElementsJobCount) parameters
	 * (SwitchServerFlowGetElementsJobCount) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SwitchServerFlowGetElementsJobCountResponse
	 * @throws Exception invalid function signature message
	 */
	public function SwitchServerFlowGetElementsJobCount($mixed = null) {
		$validParameters = array(
			"(SwitchServerFlowGetElementsJobCount)",
			"(SwitchServerFlowGetElementsJobCount)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SwitchServerFlowGetElementsJobCount", $args);
	}


	/**
	 * Service Call: SwitchServerFlowGetFullConfig
	 * Parameter options:
	 * (SwitchServerFlowGetFullConfig) parameters
	 * (SwitchServerFlowGetFullConfig) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SwitchServerFlowGetFullConfigResponse
	 * @throws Exception invalid function signature message
	 */
	public function SwitchServerFlowGetFullConfig($mixed = null) {
		$validParameters = array(
			"(SwitchServerFlowGetFullConfig)",
			"(SwitchServerFlowGetFullConfig)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SwitchServerFlowGetFullConfig", $args);
	}


	/**
	 * Service Call: SwitchServerFlowGetJobs
	 * Parameter options:
	 * (SwitchServerFlowGetJobs) parameters
	 * (SwitchServerFlowGetJobs) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SwitchServerFlowGetJobsResponse
	 * @throws Exception invalid function signature message
	 */
	public function SwitchServerFlowGetJobs($mixed = null) {
		$validParameters = array(
			"(SwitchServerFlowGetJobs)",
			"(SwitchServerFlowGetJobs)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SwitchServerFlowGetJobs", $args);
	}


	/**
	 * Service Call: SwitchServerFlowGetSubmitPoints
	 * Parameter options:
	 * (SwitchServerFlowGetSubmitPoints) parameters
	 * (SwitchServerFlowGetSubmitPoints) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SwitchServerFlowGetSubmitPointsResponse
	 * @throws Exception invalid function signature message
	 */
	public function SwitchServerFlowGetSubmitPoints($mixed = null) {
		$validParameters = array(
			"(SwitchServerFlowGetSubmitPoints)",
			"(SwitchServerFlowGetSubmitPoints)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SwitchServerFlowGetSubmitPoints", $args);
	}


	/**
	 * Service Call: SwitchServerFlowSubmitFileToFolder
	 * Parameter options:
	 * (SwitchServerFlowSubmitFileToFolder) parameters
	 * (SwitchServerFlowSubmitFileToFolder) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SwitchServerFlowSubmitFileToFolderResponse
	 * @throws Exception invalid function signature message
	 */
	public function SwitchServerFlowSubmitFileToFolder($mixed = null) {
		$validParameters = array(
			"(SwitchServerFlowSubmitFileToFolder)",
			"(SwitchServerFlowSubmitFileToFolder)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SwitchServerFlowSubmitFileToFolder", $args);
	}


	/**
	 * Service Call: SwitchServerFlowSubmitFileToSubmitPoint
	 * Parameter options:
	 * (SwitchServerFlowSubmitFileToSubmitPoint) parameters
	 * (SwitchServerFlowSubmitFileToSubmitPoint) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SwitchServerFlowSubmitFileToSubmitPointResponse
	 * @throws Exception invalid function signature message
	 */
	public function SwitchServerFlowSubmitFileToSubmitPoint($mixed = null) {
		$validParameters = array(
			"(SwitchServerFlowSubmitFileToSubmitPoint)",
			"(SwitchServerFlowSubmitFileToSubmitPoint)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SwitchServerFlowSubmitFileToSubmitPoint", $args);
	}


	/**
	 * Service Call: SwitchServerGetFlowList
	 * Parameter options:
	 * (SwitchServerGetFlowList) parameters
	 * (SwitchServerGetFlowList) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SwitchServerGetFlowListResponse
	 * @throws Exception invalid function signature message
	 */
	public function SwitchServerGetFlowList($mixed = null) {
		$validParameters = array(
			"(SwitchServerGetFlowList)",
			"(SwitchServerGetFlowList)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SwitchServerGetFlowList", $args);
	}


	/**
	 * Service Call: SwitchServerTestConnection
	 * Parameter options:
	 * (SwitchServerTestConnection) parameters
	 * (SwitchServerTestConnection) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SwitchServerTestConnectionResponse
	 * @throws Exception invalid function signature message
	 */
	public function SwitchServerTestConnection($mixed = null) {
		$validParameters = array(
			"(SwitchServerTestConnection)",
			"(SwitchServerTestConnection)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SwitchServerTestConnection", $args);
	}


	/**
	 * Service Call: TaskGetEditorCliLog
	 * Parameter options:
	 * (TaskGetEditorCliLog) parameters
	 * (TaskGetEditorCliLog) parameters
	 * @param mixed,... See function description for parameter options
	 * @return TaskGetEditorCliLogResponse
	 * @throws Exception invalid function signature message
	 */
	public function TaskGetEditorCliLog($mixed = null) {
		$validParameters = array(
			"(TaskGetEditorCliLog)",
			"(TaskGetEditorCliLog)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("TaskGetEditorCliLog", $args);
	}


	/**
	 * Service Call: TaskGetStatus
	 * Parameter options:
	 * (TaskGetStatus) parameters
	 * (TaskGetStatus) parameters
	 * @param mixed,... See function description for parameter options
	 * @return TaskGetStatusResponse
	 * @throws Exception invalid function signature message
	 */
	public function TaskGetStatus($mixed = null) {
		$validParameters = array(
			"(TaskGetStatus)",
			"(TaskGetStatus)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("TaskGetStatus", $args);
	}


	/**
	 * Service Call: TaskGetStatusAndRemoveIfCompleted
	 * Parameter options:
	 * (TaskGetStatusAndRemoveIfCompleted) parameters
	 * (TaskGetStatusAndRemoveIfCompleted) parameters
	 * @param mixed,... See function description for parameter options
	 * @return TaskGetStatusAndRemoveIfCompletedResponse
	 * @throws Exception invalid function signature message
	 */
	public function TaskGetStatusAndRemoveIfCompleted($mixed = null) {
		$validParameters = array(
			"(TaskGetStatusAndRemoveIfCompleted)",
			"(TaskGetStatusAndRemoveIfCompleted)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("TaskGetStatusAndRemoveIfCompleted", $args);
	}


	/**
	 * Service Call: TaskRemoveFromLog
	 * Parameter options:
	 * (TaskRemoveFromLog) parameters
	 * (TaskRemoveFromLog) parameters
	 * @param mixed,... See function description for parameter options
	 * @return TaskRemoveFromLogResponse
	 * @throws Exception invalid function signature message
	 */
	public function TaskRemoveFromLog($mixed = null) {
		$validParameters = array(
			"(TaskRemoveFromLog)",
			"(TaskRemoveFromLog)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("TaskRemoveFromLog", $args);
	}


	/**
	 * Service Call: TasksGetList
	 * Parameter options:
	 * (TasksGetList) parameters
	 * (TasksGetList) parameters
	 * @param mixed,... See function description for parameter options
	 * @return TasksGetListResponse
	 * @throws Exception invalid function signature message
	 */
	public function TasksGetList($mixed = null) {
		$validParameters = array(
			"(TasksGetList)",
			"(TasksGetList)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("TasksGetList", $args);
	}


	/**
	 * Service Call: TasksGetQueueOverview
	 * Parameter options:
	 * (TasksGetQueueOverview) parameters
	 * (TasksGetQueueOverview) parameters
	 * @param mixed,... See function description for parameter options
	 * @return TasksGetQueueOverviewResponse
	 * @throws Exception invalid function signature message
	 */
	public function TasksGetQueueOverview($mixed = null) {
		$validParameters = array(
			"(TasksGetQueueOverview)",
			"(TasksGetQueueOverview)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("TasksGetQueueOverview", $args);
	}


	/**
	 * Service Call: TasksGetStatusses
	 * Parameter options:
	 * (TasksGetStatusses) parameters
	 * (TasksGetStatusses) parameters
	 * @param mixed,... See function description for parameter options
	 * @return TasksGetStatussesResponse
	 * @throws Exception invalid function signature message
	 */
	public function TasksGetStatusses($mixed = null) {
		$validParameters = array(
			"(TasksGetStatusses)",
			"(TasksGetStatusses)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("TasksGetStatusses", $args);
	}


	/**
	 * Service Call: UploadExternalAsset
	 * Parameter options:
	 * (UploadExternalAsset) parameters
	 * (UploadExternalAsset) parameters
	 * @param mixed,... See function description for parameter options
	 * @return UploadExternalAssetResponse
	 * @throws Exception invalid function signature message
	 */
	public function UploadExternalAsset($mixed = null) {
		$validParameters = array(
			"(UploadExternalAsset)",
			"(UploadExternalAsset)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("UploadExternalAsset", $args);
	}


	/**
	 * Service Call: XinetExecutePortalDICall
	 * Parameter options:
	 * (XinetExecutePortalDICall) parameters
	 * (XinetExecutePortalDICall) parameters
	 * @param mixed,... See function description for parameter options
	 * @return XinetExecutePortalDICallResponse
	 * @throws Exception invalid function signature message
	 */
	public function XinetExecutePortalDICall($mixed = null) {
		$validParameters = array(
			"(XinetExecutePortalDICall)",
			"(XinetExecutePortalDICall)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("XinetExecutePortalDICall", $args);
	}


	/**
	 * Service Call: XinetSetCurrentCredentials
	 * Parameter options:
	 * (XinetSetCurrentCredentials) parameters
	 * (XinetSetCurrentCredentials) parameters
	 * @param mixed,... See function description for parameter options
	 * @return XinetSetCurrentCredentialsResponse
	 * @throws Exception invalid function signature message
	 */
	public function XinetSetCurrentCredentials($mixed = null) {
		$validParameters = array(
			"(XinetSetCurrentCredentials)",
			"(XinetSetCurrentCredentials)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("XinetSetCurrentCredentials", $args);
	}


	/**
	 * Service Call: XinetTestConnection
	 * Parameter options:
	 * (XinetTestConnection) parameters
	 * (XinetTestConnection) parameters
	 * @param mixed,... See function description for parameter options
	 * @return XinetTestConnectionResponse
	 * @throws Exception invalid function signature message
	 */
	public function XinetTestConnection($mixed = null) {
		$validParameters = array(
			"(XinetTestConnection)",
			"(XinetTestConnection)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("XinetTestConnection", $args);
	}


	/**
	 * Service Call: AdsGetFromURL
	 * Parameter options:
	 * (AdsGetFromURL) parameters
	 * (AdsGetFromURL) parameters
	 * @param mixed,... See function description for parameter options
	 * @return AdsGetFromURLResponse
	 * @throws Exception invalid function signature message
	 */
	public function AdsGetFromURL($mixed = null) {
		$validParameters = array(
			"(AdsGetFromURL)",
			"(AdsGetFromURL)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("AdsGetFromURL", $args);
	}


	/**
	 * Service Call: ApiKeyGetCurrentSettings
	 * Parameter options:
	 * (ApiKeyGetCurrentSettings) parameters
	 * (ApiKeyGetCurrentSettings) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ApiKeyGetCurrentSettingsResponse
	 * @throws Exception invalid function signature message
	 */
	public function ApiKeyGetCurrentSettings($mixed = null) {
		$validParameters = array(
			"(ApiKeyGetCurrentSettings)",
			"(ApiKeyGetCurrentSettings)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ApiKeyGetCurrentSettings", $args);
	}


	/**
	 * Service Call: ApiKeyKeepAlive
	 * Parameter options:
	 * (ApiKeyKeepAlive) parameters
	 * (ApiKeyKeepAlive) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ApiKeyKeepAliveResponse
	 * @throws Exception invalid function signature message
	 */
	public function ApiKeyKeepAlive($mixed = null) {
		$validParameters = array(
			"(ApiKeyKeepAlive)",
			"(ApiKeyKeepAlive)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ApiKeyKeepAlive", $args);
	}


	/**
	 * Service Call: AssetGetImageInfo
	 * Parameter options:
	 * (AssetGetImageInfo) parameters
	 * (AssetGetImageInfo) parameters
	 * @param mixed,... See function description for parameter options
	 * @return AssetGetImageInfoResponse
	 * @throws Exception invalid function signature message
	 */
	public function AssetGetImageInfo($mixed = null) {
		$validParameters = array(
			"(AssetGetImageInfo)",
			"(AssetGetImageInfo)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("AssetGetImageInfo", $args);
	}


	/**
	 * Service Call: BarcodeCreate
	 * Parameter options:
	 * (BarcodeCreate) parameters
	 * (BarcodeCreate) parameters
	 * @param mixed,... See function description for parameter options
	 * @return BarcodeCreateResponse
	 * @throws Exception invalid function signature message
	 */
	public function BarcodeCreate($mixed = null) {
		$validParameters = array(
			"(BarcodeCreate)",
			"(BarcodeCreate)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("BarcodeCreate", $args);
	}


	/**
	 * Service Call: BarcodeCreateColored
	 * Parameter options:
	 * (BarcodeCreateColored) parameters
	 * (BarcodeCreateColored) parameters
	 * @param mixed,... See function description for parameter options
	 * @return BarcodeCreateColoredResponse
	 * @throws Exception invalid function signature message
	 */
	public function BarcodeCreateColored($mixed = null) {
		$validParameters = array(
			"(BarcodeCreateColored)",
			"(BarcodeCreateColored)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("BarcodeCreateColored", $args);
	}


	/**
	 * Service Call: CsvFileCreate
	 * Parameter options:
	 * (CsvFileCreate) parameters
	 * (CsvFileCreate) parameters
	 * @param mixed,... See function description for parameter options
	 * @return CsvFileCreateResponse
	 * @throws Exception invalid function signature message
	 */
	public function CsvFileCreate($mixed = null) {
		$validParameters = array(
			"(CsvFileCreate)",
			"(CsvFileCreate)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("CsvFileCreate", $args);
	}


	/**
	 * Service Call: DataSourceAddSampleFile
	 * Parameter options:
	 * (DataSourceAddSampleFile) parameters
	 * (DataSourceAddSampleFile) parameters
	 * @param mixed,... See function description for parameter options
	 * @return DataSourceAddSampleFileResponse
	 * @throws Exception invalid function signature message
	 */
	public function DataSourceAddSampleFile($mixed = null) {
		$validParameters = array(
			"(DataSourceAddSampleFile)",
			"(DataSourceAddSampleFile)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("DataSourceAddSampleFile", $args);
	}


	/**
	 * Service Call: DataSourceDeleteSampleFile
	 * Parameter options:
	 * (DataSourceDeleteSampleFile) parameters
	 * (DataSourceDeleteSampleFile) parameters
	 * @param mixed,... See function description for parameter options
	 * @return DataSourceDeleteSampleFileResponse
	 * @throws Exception invalid function signature message
	 */
	public function DataSourceDeleteSampleFile($mixed = null) {
		$validParameters = array(
			"(DataSourceDeleteSampleFile)",
			"(DataSourceDeleteSampleFile)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("DataSourceDeleteSampleFile", $args);
	}


	/**
	 * Service Call: DataSourceDownloadSpreadsheets
	 * Parameter options:
	 * (DataSourceDownloadSpreadsheets) parameters
	 * (DataSourceDownloadSpreadsheets) parameters
	 * @param mixed,... See function description for parameter options
	 * @return DataSourceDownloadSpreadsheetsResponse
	 * @throws Exception invalid function signature message
	 */
	public function DataSourceDownloadSpreadsheets($mixed = null) {
		$validParameters = array(
			"(DataSourceDownloadSpreadsheets)",
			"(DataSourceDownloadSpreadsheets)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("DataSourceDownloadSpreadsheets", $args);
	}


	/**
	 * Service Call: DataSourceDownloadURL
	 * Parameter options:
	 * (DataSourceDownloadURL) parameters
	 * (DataSourceDownloadURL) parameters
	 * @param mixed,... See function description for parameter options
	 * @return DataSourceDownloadURLResponse
	 * @throws Exception invalid function signature message
	 */
	public function DataSourceDownloadURL($mixed = null) {
		$validParameters = array(
			"(DataSourceDownloadURL)",
			"(DataSourceDownloadURL)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("DataSourceDownloadURL", $args);
	}


	/**
	 * Service Call: DataSourceFileGetXML
	 * Parameter options:
	 * (DataSourceFileGetXML) parameters
	 * (DataSourceFileGetXML) parameters
	 * @param mixed,... See function description for parameter options
	 * @return DataSourceFileGetXMLResponse
	 * @throws Exception invalid function signature message
	 */
	public function DataSourceFileGetXML($mixed = null) {
		$validParameters = array(
			"(DataSourceFileGetXML)",
			"(DataSourceFileGetXML)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("DataSourceFileGetXML", $args);
	}


	/**
	 * Service Call: DataSourceListSampleFiles
	 * Parameter options:
	 * (DataSourceListSampleFiles) parameters
	 * (DataSourceListSampleFiles) parameters
	 * @param mixed,... See function description for parameter options
	 * @return DataSourceListSampleFilesResponse
	 * @throws Exception invalid function signature message
	 */
	public function DataSourceListSampleFiles($mixed = null) {
		$validParameters = array(
			"(DataSourceListSampleFiles)",
			"(DataSourceListSampleFiles)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("DataSourceListSampleFiles", $args);
	}


	/**
	 * Service Call: DataSourceSalesForceGetXML
	 * Parameter options:
	 * (DataSourceSalesForceGetXML) parameters
	 * (DataSourceSalesForceGetXML) parameters
	 * @param mixed,... See function description for parameter options
	 * @return DataSourceSalesForceGetXMLResponse
	 * @throws Exception invalid function signature message
	 */
	public function DataSourceSalesForceGetXML($mixed = null) {
		$validParameters = array(
			"(DataSourceSalesForceGetXML)",
			"(DataSourceSalesForceGetXML)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("DataSourceSalesForceGetXML", $args);
	}


	/**
	 * Service Call: DataSourceSpreadsheetGetXML
	 * Parameter options:
	 * (DataSourceSpreadsheetGetXML) parameters
	 * (DataSourceSpreadsheetGetXML) parameters
	 * @param mixed,... See function description for parameter options
	 * @return DataSourceSpreadsheetGetXMLResponse
	 * @throws Exception invalid function signature message
	 */
	public function DataSourceSpreadsheetGetXML($mixed = null) {
		$validParameters = array(
			"(DataSourceSpreadsheetGetXML)",
			"(DataSourceSpreadsheetGetXML)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("DataSourceSpreadsheetGetXML", $args);
	}


	/**
	 * Service Call: DocumentCopyAnnotations
	 * Parameter options:
	 * (DocumentCopyAnnotations) parameters
	 * (DocumentCopyAnnotations) parameters
	 * @param mixed,... See function description for parameter options
	 * @return DocumentCopyAnnotationsResponse
	 * @throws Exception invalid function signature message
	 */
	public function DocumentCopyAnnotations($mixed = null) {
		$validParameters = array(
			"(DocumentCopyAnnotations)",
			"(DocumentCopyAnnotations)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("DocumentCopyAnnotations", $args);
	}


	/**
	 * Service Call: DocumentCopyDocumentEventActions
	 * Parameter options:
	 * (DocumentCopyDocumentEventActions) parameters
	 * (DocumentCopyDocumentEventActions) parameters
	 * @param mixed,... See function description for parameter options
	 * @return DocumentCopyDocumentEventActionsResponse
	 * @throws Exception invalid function signature message
	 */
	public function DocumentCopyDocumentEventActions($mixed = null) {
		$validParameters = array(
			"(DocumentCopyDocumentEventActions)",
			"(DocumentCopyDocumentEventActions)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("DocumentCopyDocumentEventActions", $args);
	}


	/**
	 * Service Call: DocumentCopyVariableDefinitions
	 * Parameter options:
	 * (DocumentCopyVariableDefinitions) parameters
	 * (DocumentCopyVariableDefinitions) parameters
	 * @param mixed,... See function description for parameter options
	 * @return DocumentCopyVariableDefinitionsResponse
	 * @throws Exception invalid function signature message
	 */
	public function DocumentCopyVariableDefinitions($mixed = null) {
		$validParameters = array(
			"(DocumentCopyVariableDefinitions)",
			"(DocumentCopyVariableDefinitions)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("DocumentCopyVariableDefinitions", $args);
	}


	/**
	 * Service Call: DocumentCreateFromBlankDocTemplate
	 * Parameter options:
	 * (DocumentCreateFromBlankDocTemplate) parameters
	 * (DocumentCreateFromBlankDocTemplate) parameters
	 * @param mixed,... See function description for parameter options
	 * @return DocumentCreateFromBlankDocTemplateResponse
	 * @throws Exception invalid function signature message
	 */
	public function DocumentCreateFromBlankDocTemplate($mixed = null) {
		$validParameters = array(
			"(DocumentCreateFromBlankDocTemplate)",
			"(DocumentCreateFromBlankDocTemplate)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("DocumentCreateFromBlankDocTemplate", $args);
	}


	/**
	 * Service Call: DocumentCreateFromChiliPackage
	 * Parameter options:
	 * (DocumentCreateFromChiliPackage) parameters
	 * (DocumentCreateFromChiliPackage) parameters
	 * @param mixed,... See function description for parameter options
	 * @return DocumentCreateFromChiliPackageResponse
	 * @throws Exception invalid function signature message
	 */
	public function DocumentCreateFromChiliPackage($mixed = null) {
		$validParameters = array(
			"(DocumentCreateFromChiliPackage)",
			"(DocumentCreateFromChiliPackage)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("DocumentCreateFromChiliPackage", $args);
	}


	/**
	 * Service Call: DocumentCreateFromPDF
	 * Parameter options:
	 * (DocumentCreateFromPDF) parameters
	 * (DocumentCreateFromPDF) parameters
	 * @param mixed,... See function description for parameter options
	 * @return DocumentCreateFromPDFResponse
	 * @throws Exception invalid function signature message
	 */
	public function DocumentCreateFromPDF($mixed = null) {
		$validParameters = array(
			"(DocumentCreateFromPDF)",
			"(DocumentCreateFromPDF)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("DocumentCreateFromPDF", $args);
	}


	/**
	 * Service Call: DocumentCreateImages
	 * Parameter options:
	 * (DocumentCreateImages) parameters
	 * (DocumentCreateImages) parameters
	 * @param mixed,... See function description for parameter options
	 * @return DocumentCreateImagesResponse
	 * @throws Exception invalid function signature message
	 */
	public function DocumentCreateImages($mixed = null) {
		$validParameters = array(
			"(DocumentCreateImages)",
			"(DocumentCreateImages)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("DocumentCreateImages", $args);
	}


	/**
	 * Service Call: DocumentCreateImagesAndPDF
	 * Parameter options:
	 * (DocumentCreateImagesAndPDF) parameters
	 * (DocumentCreateImagesAndPDF) parameters
	 * @param mixed,... See function description for parameter options
	 * @return DocumentCreateImagesAndPDFResponse
	 * @throws Exception invalid function signature message
	 */
	public function DocumentCreateImagesAndPDF($mixed = null) {
		$validParameters = array(
			"(DocumentCreateImagesAndPDF)",
			"(DocumentCreateImagesAndPDF)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("DocumentCreateImagesAndPDF", $args);
	}


	/**
	 * Service Call: DocumentCreatePackage
	 * Parameter options:
	 * (DocumentCreatePackage) parameters
	 * (DocumentCreatePackage) parameters
	 * @param mixed,... See function description for parameter options
	 * @return DocumentCreatePackageResponse
	 * @throws Exception invalid function signature message
	 */
	public function DocumentCreatePackage($mixed = null) {
		$validParameters = array(
			"(DocumentCreatePackage)",
			"(DocumentCreatePackage)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("DocumentCreatePackage", $args);
	}


	/**
	 * Service Call: DocumentCreatePDF
	 * Parameter options:
	 * (DocumentCreatePDF) parameters
	 * (DocumentCreatePDF) parameters
	 * @param mixed,... See function description for parameter options
	 * @return DocumentCreatePDFResponse
	 * @throws Exception invalid function signature message
	 */
	public function DocumentCreatePDF($mixed = null) {
		$validParameters = array(
			"(DocumentCreatePDF)",
			"(DocumentCreatePDF)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("DocumentCreatePDF", $args);
	}


	/**
	 * Service Call: DocumentCreateTempFolding
	 * Parameter options:
	 * (DocumentCreateTempFolding) parameters
	 * (DocumentCreateTempFolding) parameters
	 * @param mixed,... See function description for parameter options
	 * @return DocumentCreateTempFoldingResponse
	 * @throws Exception invalid function signature message
	 */
	public function DocumentCreateTempFolding($mixed = null) {
		$validParameters = array(
			"(DocumentCreateTempFolding)",
			"(DocumentCreateTempFolding)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("DocumentCreateTempFolding", $args);
	}


	/**
	 * Service Call: DocumentCreateTempImages
	 * Parameter options:
	 * (DocumentCreateTempImages) parameters
	 * (DocumentCreateTempImages) parameters
	 * @param mixed,... See function description for parameter options
	 * @return DocumentCreateTempImagesResponse
	 * @throws Exception invalid function signature message
	 */
	public function DocumentCreateTempImages($mixed = null) {
		$validParameters = array(
			"(DocumentCreateTempImages)",
			"(DocumentCreateTempImages)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("DocumentCreateTempImages", $args);
	}


	/**
	 * Service Call: DocumentCreateTempImagesAndPDF
	 * Parameter options:
	 * (DocumentCreateTempImagesAndPDF) parameters
	 * (DocumentCreateTempImagesAndPDF) parameters
	 * @param mixed,... See function description for parameter options
	 * @return DocumentCreateTempImagesAndPDFResponse
	 * @throws Exception invalid function signature message
	 */
	public function DocumentCreateTempImagesAndPDF($mixed = null) {
		$validParameters = array(
			"(DocumentCreateTempImagesAndPDF)",
			"(DocumentCreateTempImagesAndPDF)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("DocumentCreateTempImagesAndPDF", $args);
	}


	/**
	 * Service Call: DocumentCreateTempPackage
	 * Parameter options:
	 * (DocumentCreateTempPackage) parameters
	 * (DocumentCreateTempPackage) parameters
	 * @param mixed,... See function description for parameter options
	 * @return DocumentCreateTempPackageResponse
	 * @throws Exception invalid function signature message
	 */
	public function DocumentCreateTempPackage($mixed = null) {
		$validParameters = array(
			"(DocumentCreateTempPackage)",
			"(DocumentCreateTempPackage)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("DocumentCreateTempPackage", $args);
	}


	/**
	 * Service Call: DocumentCreateTempPDF
	 * Parameter options:
	 * (DocumentCreateTempPDF) parameters
	 * (DocumentCreateTempPDF) parameters
	 * @param mixed,... See function description for parameter options
	 * @return DocumentCreateTempPDFResponse
	 * @throws Exception invalid function signature message
	 */
	public function DocumentCreateTempPDF($mixed = null) {
		$validParameters = array(
			"(DocumentCreateTempPDF)",
			"(DocumentCreateTempPDF)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("DocumentCreateTempPDF", $args);
	}


	/**
	 * Service Call: DocumentGetAnnotations
	 * Parameter options:
	 * (DocumentGetAnnotations) parameters
	 * (DocumentGetAnnotations) parameters
	 * @param mixed,... See function description for parameter options
	 * @return DocumentGetAnnotationsResponse
	 * @throws Exception invalid function signature message
	 */
	public function DocumentGetAnnotations($mixed = null) {
		$validParameters = array(
			"(DocumentGetAnnotations)",
			"(DocumentGetAnnotations)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("DocumentGetAnnotations", $args);
	}


	/**
	 * Service Call: DocumentGetDefaultSettings
	 * Parameter options:
	 * (DocumentGetDefaultSettings) parameters
	 * (DocumentGetDefaultSettings) parameters
	 * @param mixed,... See function description for parameter options
	 * @return DocumentGetDefaultSettingsResponse
	 * @throws Exception invalid function signature message
	 */
	public function DocumentGetDefaultSettings($mixed = null) {
		$validParameters = array(
			"(DocumentGetDefaultSettings)",
			"(DocumentGetDefaultSettings)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("DocumentGetDefaultSettings", $args);
	}


	/**
	 * Service Call: DocumentGetDocumentEventActions
	 * Parameter options:
	 * (DocumentGetDocumentEventActions) parameters
	 * (DocumentGetDocumentEventActions) parameters
	 * @param mixed,... See function description for parameter options
	 * @return DocumentGetDocumentEventActionsResponse
	 * @throws Exception invalid function signature message
	 */
	public function DocumentGetDocumentEventActions($mixed = null) {
		$validParameters = array(
			"(DocumentGetDocumentEventActions)",
			"(DocumentGetDocumentEventActions)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("DocumentGetDocumentEventActions", $args);
	}


	/**
	 * Service Call: DocumentGetEditorURL
	 * Parameter options:
	 * (DocumentGetEditorURL) parameters
	 * (DocumentGetEditorURL) parameters
	 * @param mixed,... See function description for parameter options
	 * @return DocumentGetEditorURLResponse
	 * @throws Exception invalid function signature message
	 */
	public function DocumentGetEditorURL($mixed = null) {
		$validParameters = array(
			"(DocumentGetEditorURL)",
			"(DocumentGetEditorURL)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("DocumentGetEditorURL", $args);
	}


	/**
	 * Service Call: DocumentGetFoldingViewerURL
	 * Parameter options:
	 * (DocumentGetFoldingViewerURL) parameters
	 * (DocumentGetFoldingViewerURL) parameters
	 * @param mixed,... See function description for parameter options
	 * @return DocumentGetFoldingViewerURLResponse
	 * @throws Exception invalid function signature message
	 */
	public function DocumentGetFoldingViewerURL($mixed = null) {
		$validParameters = array(
			"(DocumentGetFoldingViewerURL)",
			"(DocumentGetFoldingViewerURL)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("DocumentGetFoldingViewerURL", $args);
	}


	/**
	 * Service Call: DocumentGetInfo
	 * Parameter options:
	 * (DocumentGetInfo) parameters
	 * (DocumentGetInfo) parameters
	 * @param mixed,... See function description for parameter options
	 * @return DocumentGetInfoResponse
	 * @throws Exception invalid function signature message
	 */
	public function DocumentGetInfo($mixed = null) {
		$validParameters = array(
			"(DocumentGetInfo)",
			"(DocumentGetInfo)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("DocumentGetInfo", $args);
	}


	/**
	 * Service Call: DocumentGetIpadXML
	 * Parameter options:
	 * (DocumentGetIpadXML) parameters
	 * (DocumentGetIpadXML) parameters
	 * @param mixed,... See function description for parameter options
	 * @return DocumentGetIpadXMLResponse
	 * @throws Exception invalid function signature message
	 */
	public function DocumentGetIpadXML($mixed = null) {
		$validParameters = array(
			"(DocumentGetIpadXML)",
			"(DocumentGetIpadXML)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("DocumentGetIpadXML", $args);
	}


	/**
	 * Service Call: DocumentGetPlacedAdsAndEdit
	 * Parameter options:
	 * (DocumentGetPlacedAdsAndEdit) parameters
	 * (DocumentGetPlacedAdsAndEdit) parameters
	 * @param mixed,... See function description for parameter options
	 * @return DocumentGetPlacedAdsAndEditResponse
	 * @throws Exception invalid function signature message
	 */
	public function DocumentGetPlacedAdsAndEdit($mixed = null) {
		$validParameters = array(
			"(DocumentGetPlacedAdsAndEdit)",
			"(DocumentGetPlacedAdsAndEdit)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("DocumentGetPlacedAdsAndEdit", $args);
	}


	/**
	 * Service Call: DocumentGetPreflightResults
	 * Parameter options:
	 * (DocumentGetPreflightResults) parameters
	 * (DocumentGetPreflightResults) parameters
	 * @param mixed,... See function description for parameter options
	 * @return DocumentGetPreflightResultsResponse
	 * @throws Exception invalid function signature message
	 */
	public function DocumentGetPreflightResults($mixed = null) {
		$validParameters = array(
			"(DocumentGetPreflightResults)",
			"(DocumentGetPreflightResults)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("DocumentGetPreflightResults", $args);
	}


	/**
	 * Service Call: DocumentGetUsedAssets
	 * Parameter options:
	 * (DocumentGetUsedAssets) parameters
	 * (DocumentGetUsedAssets) parameters
	 * @param mixed,... See function description for parameter options
	 * @return DocumentGetUsedAssetsResponse
	 * @throws Exception invalid function signature message
	 */
	public function DocumentGetUsedAssets($mixed = null) {
		$validParameters = array(
			"(DocumentGetUsedAssets)",
			"(DocumentGetUsedAssets)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("DocumentGetUsedAssets", $args);
	}


	/**
	 * Service Call: DocumentGetVariableDefinitions
	 * Parameter options:
	 * (DocumentGetVariableDefinitions) parameters
	 * (DocumentGetVariableDefinitions) parameters
	 * @param mixed,... See function description for parameter options
	 * @return DocumentGetVariableDefinitionsResponse
	 * @throws Exception invalid function signature message
	 */
	public function DocumentGetVariableDefinitions($mixed = null) {
		$validParameters = array(
			"(DocumentGetVariableDefinitions)",
			"(DocumentGetVariableDefinitions)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("DocumentGetVariableDefinitions", $args);
	}


	/**
	 * Service Call: DocumentGetVariableValues
	 * Parameter options:
	 * (DocumentGetVariableValues) parameters
	 * (DocumentGetVariableValues) parameters
	 * @param mixed,... See function description for parameter options
	 * @return DocumentGetVariableValuesResponse
	 * @throws Exception invalid function signature message
	 */
	public function DocumentGetVariableValues($mixed = null) {
		$validParameters = array(
			"(DocumentGetVariableValues)",
			"(DocumentGetVariableValues)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("DocumentGetVariableValues", $args);
	}


	/**
	 * Service Call: DocumentSetAnnotations
	 * Parameter options:
	 * (DocumentSetAnnotations) parameters
	 * (DocumentSetAnnotations) parameters
	 * @param mixed,... See function description for parameter options
	 * @return DocumentSetAnnotationsResponse
	 * @throws Exception invalid function signature message
	 */
	public function DocumentSetAnnotations($mixed = null) {
		$validParameters = array(
			"(DocumentSetAnnotations)",
			"(DocumentSetAnnotations)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("DocumentSetAnnotations", $args);
	}


	/**
	 * Service Call: DocumentSetAssetDirectories
	 * Parameter options:
	 * (DocumentSetAssetDirectories) parameters
	 * (DocumentSetAssetDirectories) parameters
	 * @param mixed,... See function description for parameter options
	 * @return DocumentSetAssetDirectoriesResponse
	 * @throws Exception invalid function signature message
	 */
	public function DocumentSetAssetDirectories($mixed = null) {
		$validParameters = array(
			"(DocumentSetAssetDirectories)",
			"(DocumentSetAssetDirectories)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("DocumentSetAssetDirectories", $args);
	}


	/**
	 * Service Call: DocumentSetConstraints
	 * Parameter options:
	 * (DocumentSetConstraints) parameters
	 * (DocumentSetConstraints) parameters
	 * @param mixed,... See function description for parameter options
	 * @return DocumentSetConstraintsResponse
	 * @throws Exception invalid function signature message
	 */
	public function DocumentSetConstraints($mixed = null) {
		$validParameters = array(
			"(DocumentSetConstraints)",
			"(DocumentSetConstraints)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("DocumentSetConstraints", $args);
	}


	/**
	 * Service Call: DocumentSetDocumentEventActions
	 * Parameter options:
	 * (DocumentSetDocumentEventActions) parameters
	 * (DocumentSetDocumentEventActions) parameters
	 * @param mixed,... See function description for parameter options
	 * @return DocumentSetDocumentEventActionsResponse
	 * @throws Exception invalid function signature message
	 */
	public function DocumentSetDocumentEventActions($mixed = null) {
		$validParameters = array(
			"(DocumentSetDocumentEventActions)",
			"(DocumentSetDocumentEventActions)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("DocumentSetDocumentEventActions", $args);
	}


	/**
	 * Service Call: DocumentSetVariableDefinitions
	 * Parameter options:
	 * (DocumentSetVariableDefinitions) parameters
	 * (DocumentSetVariableDefinitions) parameters
	 * @param mixed,... See function description for parameter options
	 * @return DocumentSetVariableDefinitionsResponse
	 * @throws Exception invalid function signature message
	 */
	public function DocumentSetVariableDefinitions($mixed = null) {
		$validParameters = array(
			"(DocumentSetVariableDefinitions)",
			"(DocumentSetVariableDefinitions)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("DocumentSetVariableDefinitions", $args);
	}


	/**
	 * Service Call: DocumentSetVariableValues
	 * Parameter options:
	 * (DocumentSetVariableValues) parameters
	 * (DocumentSetVariableValues) parameters
	 * @param mixed,... See function description for parameter options
	 * @return DocumentSetVariableValuesResponse
	 * @throws Exception invalid function signature message
	 */
	public function DocumentSetVariableValues($mixed = null) {
		$validParameters = array(
			"(DocumentSetVariableValues)",
			"(DocumentSetVariableValues)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("DocumentSetVariableValues", $args);
	}


	/**
	 * Service Call: DownloadURL
	 * Parameter options:
	 * (DownloadURL) parameters
	 * (DownloadURL) parameters
	 * @param mixed,... See function description for parameter options
	 * @return DownloadURLResponse
	 * @throws Exception invalid function signature message
	 */
	public function DownloadURL($mixed = null) {
		$validParameters = array(
			"(DownloadURL)",
			"(DownloadURL)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("DownloadURL", $args);
	}


	/**
	 * Service Call: EditsGetFromURL
	 * Parameter options:
	 * (EditsGetFromURL) parameters
	 * (EditsGetFromURL) parameters
	 * @param mixed,... See function description for parameter options
	 * @return EditsGetFromURLResponse
	 * @throws Exception invalid function signature message
	 */
	public function EditsGetFromURL($mixed = null) {
		$validParameters = array(
			"(EditsGetFromURL)",
			"(EditsGetFromURL)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("EditsGetFromURL", $args);
	}


	/**
	 * Service Call: EnvironmentAdd
	 * Parameter options:
	 * (EnvironmentAdd) parameters
	 * (EnvironmentAdd) parameters
	 * @param mixed,... See function description for parameter options
	 * @return EnvironmentAddResponse
	 * @throws Exception invalid function signature message
	 */
	public function EnvironmentAdd($mixed = null) {
		$validParameters = array(
			"(EnvironmentAdd)",
			"(EnvironmentAdd)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("EnvironmentAdd", $args);
	}


	/**
	 * Service Call: EnvironmentCopy
	 * Parameter options:
	 * (EnvironmentCopy) parameters
	 * (EnvironmentCopy) parameters
	 * @param mixed,... See function description for parameter options
	 * @return EnvironmentCopyResponse
	 * @throws Exception invalid function signature message
	 */
	public function EnvironmentCopy($mixed = null) {
		$validParameters = array(
			"(EnvironmentCopy)",
			"(EnvironmentCopy)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("EnvironmentCopy", $args);
	}


	/**
	 * Service Call: EnvironmentDelete
	 * Parameter options:
	 * (EnvironmentDelete) parameters
	 * (EnvironmentDelete) parameters
	 * @param mixed,... See function description for parameter options
	 * @return EnvironmentDeleteResponse
	 * @throws Exception invalid function signature message
	 */
	public function EnvironmentDelete($mixed = null) {
		$validParameters = array(
			"(EnvironmentDelete)",
			"(EnvironmentDelete)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("EnvironmentDelete", $args);
	}


	/**
	 * Service Call: EnvironmentGetColorProfiles
	 * Parameter options:
	 * (EnvironmentGetColorProfiles) parameters
	 * (EnvironmentGetColorProfiles) parameters
	 * @param mixed,... See function description for parameter options
	 * @return EnvironmentGetColorProfilesResponse
	 * @throws Exception invalid function signature message
	 */
	public function EnvironmentGetColorProfiles($mixed = null) {
		$validParameters = array(
			"(EnvironmentGetColorProfiles)",
			"(EnvironmentGetColorProfiles)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("EnvironmentGetColorProfiles", $args);
	}


	/**
	 * Service Call: EnvironmentGetCurrent
	 * Parameter options:
	 * (EnvironmentGetCurrent) parameters
	 * (EnvironmentGetCurrent) parameters
	 * @param mixed,... See function description for parameter options
	 * @return EnvironmentGetCurrentResponse
	 * @throws Exception invalid function signature message
	 */
	public function EnvironmentGetCurrent($mixed = null) {
		$validParameters = array(
			"(EnvironmentGetCurrent)",
			"(EnvironmentGetCurrent)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("EnvironmentGetCurrent", $args);
	}


	/**
	 * Service Call: EnvironmentGetDiskUsage
	 * Parameter options:
	 * (EnvironmentGetDiskUsage) parameters
	 * (EnvironmentGetDiskUsage) parameters
	 * @param mixed,... See function description for parameter options
	 * @return EnvironmentGetDiskUsageResponse
	 * @throws Exception invalid function signature message
	 */
	public function EnvironmentGetDiskUsage($mixed = null) {
		$validParameters = array(
			"(EnvironmentGetDiskUsage)",
			"(EnvironmentGetDiskUsage)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("EnvironmentGetDiskUsage", $args);
	}


	/**
	 * Service Call: EnvironmentGetLoginSettings
	 * Parameter options:
	 * (EnvironmentGetLoginSettings) parameters
	 * (EnvironmentGetLoginSettings) parameters
	 * @param mixed,... See function description for parameter options
	 * @return EnvironmentGetLoginSettingsResponse
	 * @throws Exception invalid function signature message
	 */
	public function EnvironmentGetLoginSettings($mixed = null) {
		$validParameters = array(
			"(EnvironmentGetLoginSettings)",
			"(EnvironmentGetLoginSettings)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("EnvironmentGetLoginSettings", $args);
	}


	/**
	 * Service Call: EnvironmentGetSettings
	 * Parameter options:
	 * (EnvironmentGetSettings) parameters
	 * (EnvironmentGetSettings) parameters
	 * @param mixed,... See function description for parameter options
	 * @return EnvironmentGetSettingsResponse
	 * @throws Exception invalid function signature message
	 */
	public function EnvironmentGetSettings($mixed = null) {
		$validParameters = array(
			"(EnvironmentGetSettings)",
			"(EnvironmentGetSettings)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("EnvironmentGetSettings", $args);
	}


	/**
	 * Service Call: EnvironmentList
	 * Parameter options:
	 * (EnvironmentList) parameters
	 * (EnvironmentList) parameters
	 * @param mixed,... See function description for parameter options
	 * @return EnvironmentListResponse
	 * @throws Exception invalid function signature message
	 */
	public function EnvironmentList($mixed = null) {
		$validParameters = array(
			"(EnvironmentList)",
			"(EnvironmentList)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("EnvironmentList", $args);
	}


	/**
	 * Service Call: EnvironmentSaveSettings
	 * Parameter options:
	 * (EnvironmentSaveSettings) parameters
	 * (EnvironmentSaveSettings) parameters
	 * @param mixed,... See function description for parameter options
	 * @return EnvironmentSaveSettingsResponse
	 * @throws Exception invalid function signature message
	 */
	public function EnvironmentSaveSettings($mixed = null) {
		$validParameters = array(
			"(EnvironmentSaveSettings)",
			"(EnvironmentSaveSettings)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("EnvironmentSaveSettings", $args);
	}


	/**
	 * Service Call: FontGetIncludedGlyphs
	 * Parameter options:
	 * (FontGetIncludedGlyphs) parameters
	 * (FontGetIncludedGlyphs) parameters
	 * @param mixed,... See function description for parameter options
	 * @return FontGetIncludedGlyphsResponse
	 * @throws Exception invalid function signature message
	 */
	public function FontGetIncludedGlyphs($mixed = null) {
		$validParameters = array(
			"(FontGetIncludedGlyphs)",
			"(FontGetIncludedGlyphs)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("FontGetIncludedGlyphs", $args);
	}


	/**
	 * Service Call: GenerateApiKey
	 * Parameter options:
	 * (GenerateApiKey) parameters
	 * (GenerateApiKey) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GenerateApiKeyResponse
	 * @throws Exception invalid function signature message
	 */
	public function GenerateApiKey($mixed = null) {
		$validParameters = array(
			"(GenerateApiKey)",
			"(GenerateApiKey)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GenerateApiKey", $args);
	}


	/**
	 * Service Call: GenerateApiKeyWithSettings
	 * Parameter options:
	 * (GenerateApiKeyWithSettings) parameters
	 * (GenerateApiKeyWithSettings) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GenerateApiKeyWithSettingsResponse
	 * @throws Exception invalid function signature message
	 */
	public function GenerateApiKeyWithSettings($mixed = null) {
		$validParameters = array(
			"(GenerateApiKeyWithSettings)",
			"(GenerateApiKeyWithSettings)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GenerateApiKeyWithSettings", $args);
	}


	/**
	 * Service Call: GetServerDate
	 * Parameter options:
	 * (GetServerDate) parameters
	 * (GetServerDate) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetServerDateResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetServerDate($mixed = null) {
		$validParameters = array(
			"(GetServerDate)",
			"(GetServerDate)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetServerDate", $args);
	}


	/**
	 * Service Call: HealthCheckExecute
	 * Parameter options:
	 * (HealthCheckExecute) parameters
	 * (HealthCheckExecute) parameters
	 * @param mixed,... See function description for parameter options
	 * @return HealthCheckExecuteResponse
	 * @throws Exception invalid function signature message
	 */
	public function HealthCheckExecute($mixed = null) {
		$validParameters = array(
			"(HealthCheckExecute)",
			"(HealthCheckExecute)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("HealthCheckExecute", $args);
	}


	/**
	 * Service Call: IconSetAddIcon
	 * Parameter options:
	 * (IconSetAddIcon) parameters
	 * (IconSetAddIcon) parameters
	 * @param mixed,... See function description for parameter options
	 * @return IconSetAddIconResponse
	 * @throws Exception invalid function signature message
	 */
	public function IconSetAddIcon($mixed = null) {
		$validParameters = array(
			"(IconSetAddIcon)",
			"(IconSetAddIcon)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("IconSetAddIcon", $args);
	}


	/**
	 * Service Call: IconSetDeleteIcon
	 * Parameter options:
	 * (IconSetDeleteIcon) parameters
	 * (IconSetDeleteIcon) parameters
	 * @param mixed,... See function description for parameter options
	 * @return IconSetDeleteIconResponse
	 * @throws Exception invalid function signature message
	 */
	public function IconSetDeleteIcon($mixed = null) {
		$validParameters = array(
			"(IconSetDeleteIcon)",
			"(IconSetDeleteIcon)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("IconSetDeleteIcon", $args);
	}


	/**
	 * Service Call: IconSetGetIcons
	 * Parameter options:
	 * (IconSetGetIcons) parameters
	 * (IconSetGetIcons) parameters
	 * @param mixed,... See function description for parameter options
	 * @return IconSetGetIconsResponse
	 * @throws Exception invalid function signature message
	 */
	public function IconSetGetIcons($mixed = null) {
		$validParameters = array(
			"(IconSetGetIcons)",
			"(IconSetGetIcons)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("IconSetGetIcons", $args);
	}


	/**
	 * Service Call: InterfaceGetInitialSettings
	 * Parameter options:
	 * (InterfaceGetInitialSettings) parameters
	 * (InterfaceGetInitialSettings) parameters
	 * @param mixed,... See function description for parameter options
	 * @return InterfaceGetInitialSettingsResponse
	 * @throws Exception invalid function signature message
	 */
	public function InterfaceGetInitialSettings($mixed = null) {
		$validParameters = array(
			"(InterfaceGetInitialSettings)",
			"(InterfaceGetInitialSettings)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("InterfaceGetInitialSettings", $args);
	}


	/**
	 * Service Call: LanguageGetCombinedStrings
	 * Parameter options:
	 * (LanguageGetCombinedStrings) parameters
	 * (LanguageGetCombinedStrings) parameters
	 * @param mixed,... See function description for parameter options
	 * @return LanguageGetCombinedStringsResponse
	 * @throws Exception invalid function signature message
	 */
	public function LanguageGetCombinedStrings($mixed = null) {
		$validParameters = array(
			"(LanguageGetCombinedStrings)",
			"(LanguageGetCombinedStrings)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("LanguageGetCombinedStrings", $args);
	}


	/**
	 * Service Call: LanguageGetCsvURL
	 * Parameter options:
	 * (LanguageGetCsvURL) parameters
	 * (LanguageGetCsvURL) parameters
	 * @param mixed,... See function description for parameter options
	 * @return LanguageGetCsvURLResponse
	 * @throws Exception invalid function signature message
	 */
	public function LanguageGetCsvURL($mixed = null) {
		$validParameters = array(
			"(LanguageGetCsvURL)",
			"(LanguageGetCsvURL)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("LanguageGetCsvURL", $args);
	}


	/**
	 * Service Call: LanguageGetUnicodeTextURL
	 * Parameter options:
	 * (LanguageGetUnicodeTextURL) parameters
	 * (LanguageGetUnicodeTextURL) parameters
	 * @param mixed,... See function description for parameter options
	 * @return LanguageGetUnicodeTextURLResponse
	 * @throws Exception invalid function signature message
	 */
	public function LanguageGetUnicodeTextURL($mixed = null) {
		$validParameters = array(
			"(LanguageGetUnicodeTextURL)",
			"(LanguageGetUnicodeTextURL)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("LanguageGetUnicodeTextURL", $args);
	}


	/**
	 * Service Call: LanguageImportCsv
	 * Parameter options:
	 * (LanguageImportCsv) parameters
	 * (LanguageImportCsv) parameters
	 * @param mixed,... See function description for parameter options
	 * @return LanguageImportCsvResponse
	 * @throws Exception invalid function signature message
	 */
	public function LanguageImportCsv($mixed = null) {
		$validParameters = array(
			"(LanguageImportCsv)",
			"(LanguageImportCsv)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("LanguageImportCsv", $args);
	}


	/**
	 * Service Call: LanguageImportUnicodeText
	 * Parameter options:
	 * (LanguageImportUnicodeText) parameters
	 * (LanguageImportUnicodeText) parameters
	 * @param mixed,... See function description for parameter options
	 * @return LanguageImportUnicodeTextResponse
	 * @throws Exception invalid function signature message
	 */
	public function LanguageImportUnicodeText($mixed = null) {
		$validParameters = array(
			"(LanguageImportUnicodeText)",
			"(LanguageImportUnicodeText)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("LanguageImportUnicodeText", $args);
	}


	/**
	 * Service Call: LanguageSaveStrings
	 * Parameter options:
	 * (LanguageSaveStrings) parameters
	 * (LanguageSaveStrings) parameters
	 * @param mixed,... See function description for parameter options
	 * @return LanguageSaveStringsResponse
	 * @throws Exception invalid function signature message
	 */
	public function LanguageSaveStrings($mixed = null) {
		$validParameters = array(
			"(LanguageSaveStrings)",
			"(LanguageSaveStrings)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("LanguageSaveStrings", $args);
	}


	/**
	 * Service Call: LanguagesGetList
	 * Parameter options:
	 * (LanguagesGetList) parameters
	 * (LanguagesGetList) parameters
	 * @param mixed,... See function description for parameter options
	 * @return LanguagesGetListResponse
	 * @throws Exception invalid function signature message
	 */
	public function LanguagesGetList($mixed = null) {
		$validParameters = array(
			"(LanguagesGetList)",
			"(LanguagesGetList)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("LanguagesGetList", $args);
	}


	/**
	 * Service Call: MobileFeedGetDocumentList
	 * Parameter options:
	 * (MobileFeedGetDocumentList) parameters
	 * (MobileFeedGetDocumentList) parameters
	 * @param mixed,... See function description for parameter options
	 * @return MobileFeedGetDocumentListResponse
	 * @throws Exception invalid function signature message
	 */
	public function MobileFeedGetDocumentList($mixed = null) {
		$validParameters = array(
			"(MobileFeedGetDocumentList)",
			"(MobileFeedGetDocumentList)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("MobileFeedGetDocumentList", $args);
	}


	/**
	 * Service Call: MobileFeedGetDocumentXML
	 * Parameter options:
	 * (MobileFeedGetDocumentXML) parameters
	 * (MobileFeedGetDocumentXML) parameters
	 * @param mixed,... See function description for parameter options
	 * @return MobileFeedGetDocumentXMLResponse
	 * @throws Exception invalid function signature message
	 */
	public function MobileFeedGetDocumentXML($mixed = null) {
		$validParameters = array(
			"(MobileFeedGetDocumentXML)",
			"(MobileFeedGetDocumentXML)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("MobileFeedGetDocumentXML", $args);
	}


	/**
	 * Service Call: ProfilingClearSnapshot
	 * Parameter options:
	 * (ProfilingClearSnapshot) parameters
	 * (ProfilingClearSnapshot) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ProfilingClearSnapshotResponse
	 * @throws Exception invalid function signature message
	 */
	public function ProfilingClearSnapshot($mixed = null) {
		$validParameters = array(
			"(ProfilingClearSnapshot)",
			"(ProfilingClearSnapshot)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ProfilingClearSnapshot", $args);
	}


	/**
	 * Service Call: ProfilingSaveSnapshot
	 * Parameter options:
	 * (ProfilingSaveSnapshot) parameters
	 * (ProfilingSaveSnapshot) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ProfilingSaveSnapshotResponse
	 * @throws Exception invalid function signature message
	 */
	public function ProfilingSaveSnapshot($mixed = null) {
		$validParameters = array(
			"(ProfilingSaveSnapshot)",
			"(ProfilingSaveSnapshot)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ProfilingSaveSnapshot", $args);
	}


	/**
	 * Service Call: ResourceFolderAdd
	 * Parameter options:
	 * (ResourceFolderAdd) parameters
	 * (ResourceFolderAdd) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ResourceFolderAddResponse
	 * @throws Exception invalid function signature message
	 */
	public function ResourceFolderAdd($mixed = null) {
		$validParameters = array(
			"(ResourceFolderAdd)",
			"(ResourceFolderAdd)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ResourceFolderAdd", $args);
	}


	/**
	 * Service Call: ResourceFolderCopy
	 * Parameter options:
	 * (ResourceFolderCopy) parameters
	 * (ResourceFolderCopy) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ResourceFolderCopyResponse
	 * @throws Exception invalid function signature message
	 */
	public function ResourceFolderCopy($mixed = null) {
		$validParameters = array(
			"(ResourceFolderCopy)",
			"(ResourceFolderCopy)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ResourceFolderCopy", $args);
	}


	/**
	 * Service Call: ResourceFolderDelete
	 * Parameter options:
	 * (ResourceFolderDelete) parameters
	 * (ResourceFolderDelete) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ResourceFolderDeleteResponse
	 * @throws Exception invalid function signature message
	 */
	public function ResourceFolderDelete($mixed = null) {
		$validParameters = array(
			"(ResourceFolderDelete)",
			"(ResourceFolderDelete)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ResourceFolderDelete", $args);
	}


	/**
	 * Service Call: ResourceFolderMove
	 * Parameter options:
	 * (ResourceFolderMove) parameters
	 * (ResourceFolderMove) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ResourceFolderMoveResponse
	 * @throws Exception invalid function signature message
	 */
	public function ResourceFolderMove($mixed = null) {
		$validParameters = array(
			"(ResourceFolderMove)",
			"(ResourceFolderMove)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ResourceFolderMove", $args);
	}


	/**
	 * Service Call: ResourceGetHistory
	 * Parameter options:
	 * (ResourceGetHistory) parameters
	 * (ResourceGetHistory) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ResourceGetHistoryResponse
	 * @throws Exception invalid function signature message
	 */
	public function ResourceGetHistory($mixed = null) {
		$validParameters = array(
			"(ResourceGetHistory)",
			"(ResourceGetHistory)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ResourceGetHistory", $args);
	}


	/**
	 * Service Call: ResourceGetTree
	 * Parameter options:
	 * (ResourceGetTree) parameters
	 * (ResourceGetTree) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ResourceGetTreeResponse
	 * @throws Exception invalid function signature message
	 */
	public function ResourceGetTree($mixed = null) {
		$validParameters = array(
			"(ResourceGetTree)",
			"(ResourceGetTree)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ResourceGetTree", $args);
	}


	/**
	 * Service Call: ResourceGetTreeLevel
	 * Parameter options:
	 * (ResourceGetTreeLevel) parameters
	 * (ResourceGetTreeLevel) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ResourceGetTreeLevelResponse
	 * @throws Exception invalid function signature message
	 */
	public function ResourceGetTreeLevel($mixed = null) {
		$validParameters = array(
			"(ResourceGetTreeLevel)",
			"(ResourceGetTreeLevel)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ResourceGetTreeLevel", $args);
	}


	/**
	 * Service Call: ResourceItemAdd
	 * Parameter options:
	 * (ResourceItemAdd) parameters
	 * (ResourceItemAdd) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ResourceItemAddResponse
	 * @throws Exception invalid function signature message
	 */
	public function ResourceItemAdd($mixed = null) {
		$validParameters = array(
			"(ResourceItemAdd)",
			"(ResourceItemAdd)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ResourceItemAdd", $args);
	}


	/**
	 * Service Call: ResourceItemAddFromURL
	 * Parameter options:
	 * (ResourceItemAddFromURL) parameters
	 * (ResourceItemAddFromURL) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ResourceItemAddFromURLResponse
	 * @throws Exception invalid function signature message
	 */
	public function ResourceItemAddFromURL($mixed = null) {
		$validParameters = array(
			"(ResourceItemAddFromURL)",
			"(ResourceItemAddFromURL)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ResourceItemAddFromURL", $args);
	}


	/**
	 * Service Call: ResourceItemAddFromURLWithModificationDate
	 * Parameter options:
	 * (ResourceItemAddFromURLWithModificationDate) parameters
	 * (ResourceItemAddFromURLWithModificationDate) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ResourceItemAddFromURLWithModificationDateResponse
	 * @throws Exception invalid function signature message
	 */
	public function ResourceItemAddFromURLWithModificationDate($mixed = null) {
		$validParameters = array(
			"(ResourceItemAddFromURLWithModificationDate)",
			"(ResourceItemAddFromURLWithModificationDate)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ResourceItemAddFromURLWithModificationDate", $args);
	}


	/**
	 * Service Call: ResourceItemAddPreviewOverride
	 * Parameter options:
	 * (ResourceItemAddPreviewOverride) parameters
	 * (ResourceItemAddPreviewOverride) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ResourceItemAddPreviewOverrideResponse
	 * @throws Exception invalid function signature message
	 */
	public function ResourceItemAddPreviewOverride($mixed = null) {
		$validParameters = array(
			"(ResourceItemAddPreviewOverride)",
			"(ResourceItemAddPreviewOverride)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ResourceItemAddPreviewOverride", $args);
	}


	/**
	 * Service Call: ResourceItemAddWithPreview
	 * Parameter options:
	 * (ResourceItemAddWithPreview) parameters
	 * (ResourceItemAddWithPreview) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ResourceItemAddWithPreviewResponse
	 * @throws Exception invalid function signature message
	 */
	public function ResourceItemAddWithPreview($mixed = null) {
		$validParameters = array(
			"(ResourceItemAddWithPreview)",
			"(ResourceItemAddWithPreview)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ResourceItemAddWithPreview", $args);
	}


	/**
	 * Service Call: ResourceItemCopy
	 * Parameter options:
	 * (ResourceItemCopy) parameters
	 * (ResourceItemCopy) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ResourceItemCopyResponse
	 * @throws Exception invalid function signature message
	 */
	public function ResourceItemCopy($mixed = null) {
		$validParameters = array(
			"(ResourceItemCopy)",
			"(ResourceItemCopy)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ResourceItemCopy", $args);
	}


	/**
	 * Service Call: ResourceItemDelete
	 * Parameter options:
	 * (ResourceItemDelete) parameters
	 * (ResourceItemDelete) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ResourceItemDeleteResponse
	 * @throws Exception invalid function signature message
	 */
	public function ResourceItemDelete($mixed = null) {
		$validParameters = array(
			"(ResourceItemDelete)",
			"(ResourceItemDelete)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ResourceItemDelete", $args);
	}


	/**
	 * Service Call: ResourceItemGetByIdOrPath
	 * Parameter options:
	 * (ResourceItemGetByIdOrPath) parameters
	 * (ResourceItemGetByIdOrPath) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ResourceItemGetByIdOrPathResponse
	 * @throws Exception invalid function signature message
	 */
	public function ResourceItemGetByIdOrPath($mixed = null) {
		$validParameters = array(
			"(ResourceItemGetByIdOrPath)",
			"(ResourceItemGetByIdOrPath)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ResourceItemGetByIdOrPath", $args);
	}


	/**
	 * Service Call: ResourceItemGetByName
	 * Parameter options:
	 * (ResourceItemGetByName) parameters
	 * (ResourceItemGetByName) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ResourceItemGetByNameResponse
	 * @throws Exception invalid function signature message
	 */
	public function ResourceItemGetByName($mixed = null) {
		$validParameters = array(
			"(ResourceItemGetByName)",
			"(ResourceItemGetByName)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ResourceItemGetByName", $args);
	}


	/**
	 * Service Call: ResourceItemGetByPath
	 * Parameter options:
	 * (ResourceItemGetByPath) parameters
	 * (ResourceItemGetByPath) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ResourceItemGetByPathResponse
	 * @throws Exception invalid function signature message
	 */
	public function ResourceItemGetByPath($mixed = null) {
		$validParameters = array(
			"(ResourceItemGetByPath)",
			"(ResourceItemGetByPath)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ResourceItemGetByPath", $args);
	}


	/**
	 * Service Call: ResourceItemGetCacheInfo
	 * Parameter options:
	 * (ResourceItemGetCacheInfo) parameters
	 * (ResourceItemGetCacheInfo) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ResourceItemGetCacheInfoResponse
	 * @throws Exception invalid function signature message
	 */
	public function ResourceItemGetCacheInfo($mixed = null) {
		$validParameters = array(
			"(ResourceItemGetCacheInfo)",
			"(ResourceItemGetCacheInfo)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ResourceItemGetCacheInfo", $args);
	}


	/**
	 * Service Call: ResourceItemGetDefinitionXML
	 * Parameter options:
	 * (ResourceItemGetDefinitionXML) parameters
	 * (ResourceItemGetDefinitionXML) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ResourceItemGetDefinitionXMLResponse
	 * @throws Exception invalid function signature message
	 */
	public function ResourceItemGetDefinitionXML($mixed = null) {
		$validParameters = array(
			"(ResourceItemGetDefinitionXML)",
			"(ResourceItemGetDefinitionXML)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ResourceItemGetDefinitionXML", $args);
	}


	/**
	 * Service Call: ResourceItemGetHistory
	 * Parameter options:
	 * (ResourceItemGetHistory) parameters
	 * (ResourceItemGetHistory) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ResourceItemGetHistoryResponse
	 * @throws Exception invalid function signature message
	 */
	public function ResourceItemGetHistory($mixed = null) {
		$validParameters = array(
			"(ResourceItemGetHistory)",
			"(ResourceItemGetHistory)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ResourceItemGetHistory", $args);
	}


	/**
	 * Service Call: ResourceItemGetPrivateInfo
	 * Parameter options:
	 * (ResourceItemGetPrivateInfo) parameters
	 * (ResourceItemGetPrivateInfo) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ResourceItemGetPrivateInfoResponse
	 * @throws Exception invalid function signature message
	 */
	public function ResourceItemGetPrivateInfo($mixed = null) {
		$validParameters = array(
			"(ResourceItemGetPrivateInfo)",
			"(ResourceItemGetPrivateInfo)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ResourceItemGetPrivateInfo", $args);
	}


	/**
	 * Service Call: ResourceItemGetTransformedURL
	 * Parameter options:
	 * (ResourceItemGetTransformedURL) parameters
	 * (ResourceItemGetTransformedURL) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ResourceItemGetTransformedURLResponse
	 * @throws Exception invalid function signature message
	 */
	public function ResourceItemGetTransformedURL($mixed = null) {
		$validParameters = array(
			"(ResourceItemGetTransformedURL)",
			"(ResourceItemGetTransformedURL)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ResourceItemGetTransformedURL", $args);
	}


	/**
	 * Service Call: ResourceItemGetTransformedURLWithDebugInfo
	 * Parameter options:
	 * (ResourceItemGetTransformedURLWithDebugInfo) parameters
	 * (ResourceItemGetTransformedURLWithDebugInfo) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ResourceItemGetTransformedURLWithDebugInfoResponse
	 * @throws Exception invalid function signature message
	 */
	public function ResourceItemGetTransformedURLWithDebugInfo($mixed = null) {
		$validParameters = array(
			"(ResourceItemGetTransformedURLWithDebugInfo)",
			"(ResourceItemGetTransformedURLWithDebugInfo)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ResourceItemGetTransformedURLWithDebugInfo", $args);
	}


	/**
	 * Service Call: ResourceItemGetURL
	 * Parameter options:
	 * (ResourceItemGetURL) parameters
	 * (ResourceItemGetURL) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ResourceItemGetURLResponse
	 * @throws Exception invalid function signature message
	 */
	public function ResourceItemGetURL($mixed = null) {
		$validParameters = array(
			"(ResourceItemGetURL)",
			"(ResourceItemGetURL)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ResourceItemGetURL", $args);
	}


	/**
	 * Service Call: ResourceItemGetURLForAnonymousUser
	 * Parameter options:
	 * (ResourceItemGetURLForAnonymousUser) parameters
	 * (ResourceItemGetURLForAnonymousUser) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ResourceItemGetURLForAnonymousUserResponse
	 * @throws Exception invalid function signature message
	 */
	public function ResourceItemGetURLForAnonymousUser($mixed = null) {
		$validParameters = array(
			"(ResourceItemGetURLForAnonymousUser)",
			"(ResourceItemGetURLForAnonymousUser)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ResourceItemGetURLForAnonymousUser", $args);
	}


	/**
	 * Service Call: ResourceItemGetURLWithDebugInfo
	 * Parameter options:
	 * (ResourceItemGetURLWithDebugInfo) parameters
	 * (ResourceItemGetURLWithDebugInfo) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ResourceItemGetURLWithDebugInfoResponse
	 * @throws Exception invalid function signature message
	 */
	public function ResourceItemGetURLWithDebugInfo($mixed = null) {
		$validParameters = array(
			"(ResourceItemGetURLWithDebugInfo)",
			"(ResourceItemGetURLWithDebugInfo)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ResourceItemGetURLWithDebugInfo", $args);
	}


	/**
	 * Service Call: ResourceItemGetXML
	 * Parameter options:
	 * (ResourceItemGetXML) parameters
	 * (ResourceItemGetXML) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ResourceItemGetXMLResponse
	 * @throws Exception invalid function signature message
	 */
	public function ResourceItemGetXML($mixed = null) {
		$validParameters = array(
			"(ResourceItemGetXML)",
			"(ResourceItemGetXML)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ResourceItemGetXML", $args);
	}


	/**
	 * Service Call: ResourceItemMove
	 * Parameter options:
	 * (ResourceItemMove) parameters
	 * (ResourceItemMove) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ResourceItemMoveResponse
	 * @throws Exception invalid function signature message
	 */
	public function ResourceItemMove($mixed = null) {
		$validParameters = array(
			"(ResourceItemMove)",
			"(ResourceItemMove)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ResourceItemMove", $args);
	}


	/**
	 * Service Call: ResourceItemRemovePreviewOverride
	 * Parameter options:
	 * (ResourceItemRemovePreviewOverride) parameters
	 * (ResourceItemRemovePreviewOverride) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ResourceItemRemovePreviewOverrideResponse
	 * @throws Exception invalid function signature message
	 */
	public function ResourceItemRemovePreviewOverride($mixed = null) {
		$validParameters = array(
			"(ResourceItemRemovePreviewOverride)",
			"(ResourceItemRemovePreviewOverride)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ResourceItemRemovePreviewOverride", $args);
	}


	/**
	 * Service Call: ResourceItemReplaceFile
	 * Parameter options:
	 * (ResourceItemReplaceFile) parameters
	 * (ResourceItemReplaceFile) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ResourceItemReplaceFileResponse
	 * @throws Exception invalid function signature message
	 */
	public function ResourceItemReplaceFile($mixed = null) {
		$validParameters = array(
			"(ResourceItemReplaceFile)",
			"(ResourceItemReplaceFile)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ResourceItemReplaceFile", $args);
	}


	/**
	 * Service Call: ResourceItemReplaceFileWithPreviewOverride
	 * Parameter options:
	 * (ResourceItemReplaceFileWithPreviewOverride) parameters
	 * (ResourceItemReplaceFileWithPreviewOverride) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ResourceItemReplaceFileWithPreviewOverrideResponse
	 * @throws Exception invalid function signature message
	 */
	public function ResourceItemReplaceFileWithPreviewOverride($mixed = null) {
		$validParameters = array(
			"(ResourceItemReplaceFileWithPreviewOverride)",
			"(ResourceItemReplaceFileWithPreviewOverride)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ResourceItemReplaceFileWithPreviewOverride", $args);
	}


	/**
	 * Service Call: ResourceItemResetPreviews
	 * Parameter options:
	 * (ResourceItemResetPreviews) parameters
	 * (ResourceItemResetPreviews) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ResourceItemResetPreviewsResponse
	 * @throws Exception invalid function signature message
	 */
	public function ResourceItemResetPreviews($mixed = null) {
		$validParameters = array(
			"(ResourceItemResetPreviews)",
			"(ResourceItemResetPreviews)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ResourceItemResetPreviews", $args);
	}


	/**
	 * Service Call: ResourceItemsAddFromZip
	 * Parameter options:
	 * (ResourceItemsAddFromZip) parameters
	 * (ResourceItemsAddFromZip) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ResourceItemsAddFromZipResponse
	 * @throws Exception invalid function signature message
	 */
	public function ResourceItemsAddFromZip($mixed = null) {
		$validParameters = array(
			"(ResourceItemsAddFromZip)",
			"(ResourceItemsAddFromZip)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ResourceItemsAddFromZip", $args);
	}


	/**
	 * Service Call: ResourceItemSave
	 * Parameter options:
	 * (ResourceItemSave) parameters
	 * (ResourceItemSave) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ResourceItemSaveResponse
	 * @throws Exception invalid function signature message
	 */
	public function ResourceItemSave($mixed = null) {
		$validParameters = array(
			"(ResourceItemSave)",
			"(ResourceItemSave)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ResourceItemSave", $args);
	}


	/**
	 * Service Call: ResourceLibraryGetSettings
	 * Parameter options:
	 * (ResourceLibraryGetSettings) parameters
	 * (ResourceLibraryGetSettings) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ResourceLibraryGetSettingsResponse
	 * @throws Exception invalid function signature message
	 */
	public function ResourceLibraryGetSettings($mixed = null) {
		$validParameters = array(
			"(ResourceLibraryGetSettings)",
			"(ResourceLibraryGetSettings)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ResourceLibraryGetSettings", $args);
	}


	/**
	 * Service Call: ResourceLibrarySaveSettings
	 * Parameter options:
	 * (ResourceLibrarySaveSettings) parameters
	 * (ResourceLibrarySaveSettings) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ResourceLibrarySaveSettingsResponse
	 * @throws Exception invalid function signature message
	 */
	public function ResourceLibrarySaveSettings($mixed = null) {
		$validParameters = array(
			"(ResourceLibrarySaveSettings)",
			"(ResourceLibrarySaveSettings)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ResourceLibrarySaveSettings", $args);
	}


	/**
	 * Service Call: ResourceList
	 * Parameter options:
	 * (ResourceList) parameters
	 * (ResourceList) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ResourceListResponse
	 * @throws Exception invalid function signature message
	 */
	public function ResourceList($mixed = null) {
		$validParameters = array(
			"(ResourceList)",
			"(ResourceList)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ResourceList", $args);
	}


	/**
	 * Service Call: ResourceSearch
	 * Parameter options:
	 * (ResourceSearch) parameters
	 * (ResourceSearch) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ResourceSearchResponse
	 * @throws Exception invalid function signature message
	 */
	public function ResourceSearch($mixed = null) {
		$validParameters = array(
			"(ResourceSearch)",
			"(ResourceSearch)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ResourceSearch", $args);
	}


	/**
	 * Service Call: ResourceSearchByIDs
	 * Parameter options:
	 * (ResourceSearchByIDs) parameters
	 * (ResourceSearchByIDs) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ResourceSearchByIDsResponse
	 * @throws Exception invalid function signature message
	 */
	public function ResourceSearchByIDs($mixed = null) {
		$validParameters = array(
			"(ResourceSearchByIDs)",
			"(ResourceSearchByIDs)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ResourceSearchByIDs", $args);
	}


	/**
	 * Service Call: ResourceSearchInFolder
	 * Parameter options:
	 * (ResourceSearchInFolder) parameters
	 * (ResourceSearchInFolder) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ResourceSearchInFolderResponse
	 * @throws Exception invalid function signature message
	 */
	public function ResourceSearchInFolder($mixed = null) {
		$validParameters = array(
			"(ResourceSearchInFolder)",
			"(ResourceSearchInFolder)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ResourceSearchInFolder", $args);
	}


	/**
	 * Service Call: ResourceSearchPaged
	 * Parameter options:
	 * (ResourceSearchPaged) parameters
	 * (ResourceSearchPaged) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ResourceSearchPagedResponse
	 * @throws Exception invalid function signature message
	 */
	public function ResourceSearchPaged($mixed = null) {
		$validParameters = array(
			"(ResourceSearchPaged)",
			"(ResourceSearchPaged)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ResourceSearchPaged", $args);
	}


	/**
	 * Service Call: ServerDeleteAllSaveSystemFileInfos
	 * Parameter options:
	 * (ServerDeleteAllSaveSystemFileInfos) parameters
	 * (ServerDeleteAllSaveSystemFileInfos) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ServerDeleteAllSaveSystemFileInfosResponse
	 * @throws Exception invalid function signature message
	 */
	public function ServerDeleteAllSaveSystemFileInfos($mixed = null) {
		$validParameters = array(
			"(ServerDeleteAllSaveSystemFileInfos)",
			"(ServerDeleteAllSaveSystemFileInfos)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ServerDeleteAllSaveSystemFileInfos", $args);
	}


	/**
	 * Service Call: ServerDeleteSavedSystemInfoXML
	 * Parameter options:
	 * (ServerDeleteSavedSystemInfoXML) parameters
	 * (ServerDeleteSavedSystemInfoXML) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ServerDeleteSavedSystemInfoXMLResponse
	 * @throws Exception invalid function signature message
	 */
	public function ServerDeleteSavedSystemInfoXML($mixed = null) {
		$validParameters = array(
			"(ServerDeleteSavedSystemInfoXML)",
			"(ServerDeleteSavedSystemInfoXML)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ServerDeleteSavedSystemInfoXML", $args);
	}


	/**
	 * Service Call: ServerGetLoggingSettings
	 * Parameter options:
	 * (ServerGetLoggingSettings) parameters
	 * (ServerGetLoggingSettings) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ServerGetLoggingSettingsResponse
	 * @throws Exception invalid function signature message
	 */
	public function ServerGetLoggingSettings($mixed = null) {
		$validParameters = array(
			"(ServerGetLoggingSettings)",
			"(ServerGetLoggingSettings)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ServerGetLoggingSettings", $args);
	}


	/**
	 * Service Call: ServerGetSavedSystemInfoList
	 * Parameter options:
	 * (ServerGetSavedSystemInfoList) parameters
	 * (ServerGetSavedSystemInfoList) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ServerGetSavedSystemInfoListResponse
	 * @throws Exception invalid function signature message
	 */
	public function ServerGetSavedSystemInfoList($mixed = null) {
		$validParameters = array(
			"(ServerGetSavedSystemInfoList)",
			"(ServerGetSavedSystemInfoList)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ServerGetSavedSystemInfoList", $args);
	}


	/**
	 * Service Call: ServerGetSavedSystemInfoXML
	 * Parameter options:
	 * (ServerGetSavedSystemInfoXML) parameters
	 * (ServerGetSavedSystemInfoXML) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ServerGetSavedSystemInfoXMLResponse
	 * @throws Exception invalid function signature message
	 */
	public function ServerGetSavedSystemInfoXML($mixed = null) {
		$validParameters = array(
			"(ServerGetSavedSystemInfoXML)",
			"(ServerGetSavedSystemInfoXML)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ServerGetSavedSystemInfoXML", $args);
	}


	/**
	 * Service Call: ServerGetSettings
	 * Parameter options:
	 * (ServerGetSettings) parameters
	 * (ServerGetSettings) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ServerGetSettingsResponse
	 * @throws Exception invalid function signature message
	 */
	public function ServerGetSettings($mixed = null) {
		$validParameters = array(
			"(ServerGetSettings)",
			"(ServerGetSettings)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ServerGetSettings", $args);
	}


	/**
	 * Service Call: ServerGetSystemInfo
	 * Parameter options:
	 * (ServerGetSystemInfo) parameters
	 * (ServerGetSystemInfo) parameters
	 * @param mixed,... See function description for parameter options
	 * @return ServerGetSystemInfoResponse
	 * @throws Exception invalid function signature message
	 */
	public function ServerGetSystemInfo($mixed = null) {
		$validParameters = array(
			"(ServerGetSystemInfo)",
			"(ServerGetSystemInfo)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("ServerGetSystemInfo", $args);
	}


}}

?>