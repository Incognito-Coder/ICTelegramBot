<?php

namespace TelegramBot;

error_reporting(0);

/**
 * ICTelegramBot - Modern PHP Telegram Bot Library
 * Based on the Official Telegram Bot API (supporting Bot API 7.x - 8.3+)
 *
 * @author Incognito Coder
 * @copyright 2020-2026 ICDev
 * @version 2.0.0
 * @link https://github.com/Incognito-Coder/ICTelegramBot
 */
class ICBot
{
    // --- Message / Content Type Constants ---
    const TEXT = 'text';
    const PHOTO = 'photo';
    const VIDEO = 'video';
    const DOCUMENT = 'document';
    const AUDIO = 'music';
    const VOICE = 'voice';
    const CONTACT = 'contact';
    const ANIMATION = 'animation';
    const STICKER = 'sticker';
    const VIDEO_NOTE = 'video_note';
    const LOCATION = 'location';
    const VENUE = 'venue';
    const POLL = 'poll';
    const DICE = 'dice';
    const GAME = 'game';
    const INVOICE = 'invoice';
    const SUCCESSFUL_PAYMENT = 'successful_payment';
    const PAID_MEDIA = 'paid_media';
    const STORY = 'story';

    // --- Update Type Constants ---
    const CALLBACK_QUERY = 'callback_query';
    const INLINE_QUERY = 'inline_query';
    const CHOSEN_INLINE_RESULT = 'chosen_inline_result';
    const SHIPPING_QUERY = 'shipping_query';
    const PRE_CHECKOUT_QUERY = 'pre_checkout_query';
    const POLL_UPDATE = 'poll';
    const POLL_ANSWER = 'poll_answer';
    const MY_CHAT_MEMBER = 'my_chat_member';
    const CHAT_MEMBER = 'chat_member';
    const CHAT_JOIN_REQUEST = 'chat_join_request';
    const CHAT_BOOST = 'chat_boost';
    const REMOVED_CHAT_BOOST = 'removed_chat_boost';
    const MESSAGE_REACTION = 'message_reaction';
    const MESSAGE_REACTION_COUNT = 'message_reaction_count';
    const BUSINESS_CONNECTION = 'business_connection';
    const BUSINESS_MESSAGE = 'business_message';
    const EDITED_BUSINESS_MESSAGE = 'edited_business_message';
    const DELETED_BUSINESS_MESSAGES = 'deleted_business_messages';
    const PURCHASED_PAID_MEDIA = 'purchased_paid_media';
    const EDITED_MESSAGE = 'edited_message';
    const CHANNEL_POST = 'channel_post';
    const EDITED_CHANNEL_POST = 'edited_channel_post';

    // --- Parse Mode Constants ---
    const PARSE_HTML = 'HTML';
    const PARSE_MARKDOWN = 'Markdown';
    const PARSE_MARKDOWN_V2 = 'MarkdownV2';

    // --- Chat Action Constants ---
    const ACTION_TYPING = 'typing';
    const ACTION_UPLOAD_PHOTO = 'upload_photo';
    const ACTION_RECORD_VIDEO = 'record_video';
    const ACTION_UPLOAD_VIDEO = 'upload_video';
    const ACTION_RECORD_VOICE = 'record_voice';
    const ACTION_UPLOAD_VOICE = 'upload_voice';
    const ACTION_UPLOAD_DOCUMENT = 'upload_document';
    const ACTION_CHOOSE_STICKER = 'choose_sticker';
    const ACTION_FIND_LOCATION = 'find_location';
    const ACTION_RECORD_VIDEO_NOTE = 'record_video_note';
    const ACTION_UPLOAD_VIDEO_NOTE = 'upload_video_note';

    // --- Dice Emoji Constants ---
    const DICE_DEFAULT = '🎲';
    const DICE_DART = '🎯';
    const DICE_BASKETBALL = '🏀';
    const DICE_FOOTBALL = '⚽';
    const DICE_BOWLING = '🎳';
    const DICE_SLOT = '🎰';

    /**
     * Raw parsed update data
     * @var array
     */
    private $Data = [];

    /**
     * Stored variables for backward compatibility
     * @var array
     */
    private $Array = [];

    /**
     * Bot API Token
     * @var string|null
     */
    protected $Token = null;

    /**
     * Proxy address (host:port or ip:port)
     * @var string|null
     */
    protected $Proxy = null;

    /**
     * Proxy authentication (username:password)
     * @var string|null
     */
    protected $ProxyAuth = null;

    /**
     * Proxy type (HTTP, SOCKS4, SOCKS5)
     * @var string|null
     */
    protected $ProxyType = null;

    /**
     * Custom Bot API base URL (useful for local Bot API servers)
     * @var string
     */
    protected $ApiUrl = 'https://api.telegram.org';

    /**
     * Last cURL error message if any
     * @var string|null
     */
    protected $LastError = null;

    /**
     * Last raw response from Telegram
     * @var string|null
     */
    protected $LastRawResponse = null;

    /**
     * Last decoded response
     * @var mixed
     */
    protected $LastResponse = null;

    /**
     * Whether to decode JSON responses as associative arrays
     * @var bool
     */
    protected $AsArray = false;

    /**
     * ICBot constructor.
     *
     * @param string|null $Token Bot token from @BotFather (optional, can also call Initialize)
     * @param string|null $Proxy Proxy URL/IP:Port (optional)
     * @param string|null $ProxyAuth Proxy username:password (optional)
     * @param string|null $ProxyType Proxy type HTTP/SOCKS4/SOCKS5 (optional)
     * @param string|null $ApiUrl Custom Bot API server endpoint (optional)
     */
    public function __construct($Token = null, $Proxy = null, $ProxyAuth = null, $ProxyType = null, $ApiUrl = null)
    {
        $this->Data = $this->Update();

        if (!empty($Token)) {
            $this->Initialize($Token, $Proxy, $ProxyAuth, $ProxyType, $ApiUrl);
        }
    }

    /**
     * Reads incoming update from webhook payload.
     *
     * @return array
     */
    public function Update()
    {
        if (empty($this->Data)) {
            $input = file_get_contents('php://input');
            if (!empty($input)) {
                $decoded = json_decode($input, true);
                if (is_array($decoded)) {
                    $this->Data = $decoded;
                    return $this->Data;
                }
            }
            return [];
        }

        return $this->Data;
    }

    /**
     * Sets update data manually (useful for testing or long polling loops).
     *
     * @param array $update
     * @return $this
     */
    public function SetUpdate(array $update)
    {
        $this->Data = $update;
        return $this;
    }

    /**
     * Returns raw update data.
     *
     * @return array
     */
    public function GetData()
    {
        return $this->Data;
    }

    /**
     * Alias of GetData().
     *
     * @return array
     */
    public function RawUpdate()
    {
        return $this->Data;
    }

    /**
     * Configures the bot credentials and proxy settings.
     *
     * @param string $Token Your Bot API-KEY, Get It From @BotFather
     * @param string|null $Proxy Proxy address (host:port or ip:port)
     * @param string|null $ProxyAuth Proxy username:password
     * @param string|null $ProxyType Proxy type (HTTP, SOCKS4, SOCKS5)
     * @param string|null $ApiUrl Custom Bot API base URL (defaults to https://api.telegram.org)
     * @return $this
     */
    public function Initialize($Token, $Proxy = null, $ProxyAuth = null, $ProxyType = null, $ApiUrl = null)
    {
        $this->Token = (string)$Token;

        if (!defined('API_KEY')) {
            define('API_KEY', $this->Token);
        }

        if (!empty($Proxy)) {
            $this->Proxy = $Proxy;
            $GLOBALS['ICBOT_PROXY'] = $Proxy;
        }
        if (!empty($ProxyAuth)) {
            $this->ProxyAuth = $ProxyAuth;
            $GLOBALS['ICBOT_PROXY_AUTH'] = $ProxyAuth;
        }
        if (!empty($ProxyType)) {
            $this->ProxyType = strtoupper($ProxyType);
            $GLOBALS['ICBOT_PROXY_TYPE'] = $this->ProxyType;
        }
        if (!empty($ApiUrl)) {
            $this->ApiUrl = rtrim($ApiUrl, '/');
            $GLOBALS['ICBOT_API_URL'] = $this->ApiUrl;
        }

        $GLOBALS['ICBOT_INSTANCE'] = $this;

        // Register global BOT helper function safely if not already defined
        if (!function_exists(__NAMESPACE__ . '\BOT')) {
            /**
             * Universal Telegram Bot API request function.
             *
             * @param string $Method API method name (e.g. 'sendMessage')
             * @param array $Data Request parameters
             * @return mixed
             */
            function BOT($Method, $Data = [])
            {
                if (isset($GLOBALS['ICBOT_INSTANCE']) && $GLOBALS['ICBOT_INSTANCE'] instanceof ICBot) {
                    return $GLOBALS['ICBOT_INSTANCE']->Request($Method, $Data);
                }
                return ICBot::StaticRequest($Method, $Data);
            }
        }

        return $this;
    }

    /**
     * Sets whether responses should be returned as associative arrays or stdClass objects.
     *
     * @param bool $asArray
     * @return $this
     */
    public function SetAsArray($asArray = true)
    {
        $this->AsArray = (bool)$asArray;
        return $this;
    }

    /**
     * Executes an API request to Telegram Bot API.
     *
     * @param string $Method Telegram Bot API method name
     * @param array $Data Parameters for the API call
     * @return mixed Decoded Telegram response or false on failure
     */
    public function Request($Method, array $Data = [])
    {
        $token = $this->Token ?: (defined('API_KEY') ? API_KEY : null);
        if (empty($token)) {
            $this->LastError = 'Bot token is not set. Call Initialize($Token) first.';
            return false;
        }

        $url = rtrim($this->ApiUrl, '/') . '/bot' . $token . '/' . $Method;

        // Prepare parameters (convert nested arrays/objects to JSON strings for Telegram multipart/form-data)
        $postData = [];
        foreach ($Data as $key => $value) {
            if ($value === null) {
                continue;
            }

            if (is_array($value) || is_object($value)) {
                // If it's a CURLFile, pass it directly
                if ($value instanceof \CURLFile) {
                    $postData[$key] = $value;
                } else {
                    $postData[$key] = json_encode($value);
                }
            } elseif (is_string($value) && (in_array($key, ['photo', 'video', 'audio', 'voice', 'document', 'animation', 'sticker', 'video_note', 'thumb', 'thumbnail', 'certificate']) || substr($key, -5) === '_file')) {
                // Automatically wrap existing local files into CURLFile if file exists on disk
                if (!preg_match('/^(https?:\/\/|tg:\/\/)/i', $value) && file_exists($value) && is_file($value)) {
                    $postData[$key] = new \CURLFile($value);
                } else {
                    $postData[$key] = $value;
                }
            } else {
                $postData[$key] = $value;
            }
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        // Proxy configuration
        $proxy = $this->Proxy ?: ($GLOBALS['ICBOT_PROXY'] ?? null);
        $proxyAuth = $this->ProxyAuth ?: ($GLOBALS['ICBOT_PROXY_AUTH'] ?? null);
        $proxyType = $this->ProxyType ?: ($GLOBALS['ICBOT_PROXY_TYPE'] ?? null);

        if (!empty($proxy)) {
            curl_setopt($ch, CURLOPT_PROXY, $proxy);
            if (!empty($proxyAuth)) {
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyAuth);
            }
            if (!empty($proxyType)) {
                switch (strtoupper($proxyType)) {
                    case 'SOCKS5':
                        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
                        break;
                    case 'SOCKS4':
                        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS4);
                        break;
                    default:
                        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
                        break;
                }
            }
        }

        $res = curl_exec($ch);
        $this->LastRawResponse = $res;

        if (curl_errno($ch)) {
            $this->LastError = curl_error($ch);
            curl_close($ch);
            return false;
        }

        curl_close($ch);
        $this->LastError = null;

        $decoded = json_decode($res, $this->AsArray);
        $this->LastResponse = $decoded;

        return $decoded;
    }

    /**
     * Fallback static request engine when called via BOT() before instance setup.
     *
     * @param string $Method
     * @param array $Data
     * @return mixed
     */
    public static function StaticRequest($Method, array $Data = [])
    {
        $token = defined('API_KEY') ? API_KEY : null;
        if (empty($token)) {
            return false;
        }

        $apiUrl = $GLOBALS['ICBOT_API_URL'] ?? 'https://api.telegram.org';
        $url = rtrim($apiUrl, '/') . '/bot' . $token . '/' . $Method;

        $postData = [];
        foreach ($Data as $key => $value) {
            if ($value === null) {
                continue;
            }
            if (is_array($value) || is_object($value)) {
                if ($value instanceof \CURLFile) {
                    $postData[$key] = $value;
                } else {
                    $postData[$key] = json_encode($value);
                }
            } elseif (is_string($value) && file_exists($value) && is_file($value) && !preg_match('/^(https?:\/\/)/i', $value)) {
                $postData[$key] = new \CURLFile($value);
            } else {
                $postData[$key] = $value;
            }
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        if (!empty($GLOBALS['ICBOT_PROXY'])) {
            curl_setopt($ch, CURLOPT_PROXY, $GLOBALS['ICBOT_PROXY']);
            if (!empty($GLOBALS['ICBOT_PROXY_AUTH'])) {
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $GLOBALS['ICBOT_PROXY_AUTH']);
            }
            if (!empty($GLOBALS['ICBOT_PROXY_TYPE'])) {
                switch (strtoupper($GLOBALS['ICBOT_PROXY_TYPE'])) {
                    case 'SOCKS5':
                        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
                        break;
                    case 'SOCKS4':
                        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS4);
                        break;
                    default:
                        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
                        break;
                }
            }
        }

        $res = curl_exec($ch);
        if (curl_errno($ch)) {
            curl_close($ch);
            return false;
        }

        curl_close($ch);
        return json_decode($res);
    }

    /**
     * Direct alias for Request(). Call any Telegram API method directly.
     *
     * @param string $Method
     * @param array $Data
     * @return mixed
     */
    public function Endpoint($Method, array $Data = [])
    {
        return $this->Request($Method, $Data);
    }

    /**
     * Returns last cURL error string if any.
     *
     * @return string|null
     */
    public function GetLastError()
    {
        return $this->LastError;
    }

    /**
     * Returns last raw response body.
     *
     * @return string|null
     */
    public function GetLastRawResponse()
    {
        return $this->LastRawResponse;
    }

    /**
     * Returns last decoded response.
     *
     * @return mixed
     */
    public function GetLastResponse()
    {
        return $this->LastResponse;
    }

    /**
     * Checks if a response from Telegram Bot API succeeded (ok == true).
     *
     * @param mixed $response
     * @return bool
     */
    public function IsOk($response = null)
    {
        $res = $response !== null ? $response : $this->LastResponse;
        if (is_object($res)) {
            return !empty($res->ok);
        }
        if (is_array($res)) {
            return !empty($res['ok']);
        }
        return false;
    }

    // =========================================================================
    // WEBHOOK & GETUPDATES (POLLING)
    // =========================================================================

    /**
     * Sets webhook URL for Telegram Bot API.
     *
     * @param string $Input Webhook URL.
     * @param array $Options Additional webhook options (certificate, ip_address, max_connections, allowed_updates, drop_pending_updates, secret_token)
     * @return mixed
     */
    public function SetWebHook($Input, array $Options = [])
    {
        $params = array_merge(['url' => $Input], $Options);
        return $this->Request('setWebhook', $params);
    }

    /**
     * Removes webhook integration.
     *
     * @param bool $drop_pending_updates Pass true to drop all pending updates
     * @return mixed
     */
    public function DeleteWebHook($drop_pending_updates = false)
    {
        return $this->Request('deleteWebhook', [
            'drop_pending_updates' => (bool)$drop_pending_updates
        ]);
    }

    /**
     * Returns current webhook status.
     *
     * @return mixed
     */
    public function GetWebhookInfo()
    {
        return $this->Request('getWebhookInfo');
    }

    /**
     * Receives incoming updates using long polling.
     *
     * @param array $Options (offset, limit, timeout, allowed_updates)
     * @return mixed
     */
    public function GetUpdates(array $Options = [])
    {
        return $this->Request('getUpdates', $Options);
    }

    // =========================================================================
    // SENDING MESSAGES & MEDIA
    // =========================================================================

    /**
     * Sends a text message.
     *
     * @param mixed $chat Target ChatID.
     * @param string $text Body of Message To Send.
     * @param string|null $parse Parse mode: HTML, Markdown, MarkdownV2 (Optional)
     * @param bool|array|null $preview Disables link previews (bool) or LinkPreviewOptions (array) (Optional)
     * @param bool|null $notification Sends the message silently (Optional)
     * @param int|array|null $reply Message ID or ReplyParameters array (Optional)
     * @param mixed $keyboard Inline / Reply Keyboard markup (Optional)
     * @param array $extra Additional Telegram parameters (message_thread_id, message_effect_id, business_connection_id, etc.)
     * @return mixed
     */
    public function SendMessage($chat, $text, $parse = null, $preview = null, $notification = null, $reply = null, $keyboard = null, array $extra = [])
    {
        $data = [
            'chat_id' => $chat,
            'text' => $text,
            'parse_mode' => $parse,
            'disable_notification' => $notification,
            'reply_markup' => $keyboard,
        ];

        if (is_array($preview)) {
            $data['link_preview_options'] = $preview;
        } elseif ($preview !== null) {
            $data['disable_web_page_preview'] = (bool)$preview;
        }

        if (is_array($reply)) {
            $data['reply_parameters'] = $reply;
        } elseif ($reply !== null) {
            $data['reply_to_message_id'] = $reply;
        }

        return $this->Request('sendMessage', array_merge($data, $extra));
    }

    /**
     * Copies a message of any kind.
     *
     * @param mixed $chat Target ChatID
     * @param mixed $from_chat Source ChatID
     * @param int $msgid Message ID to copy
     * @param string|null $caption New caption (Optional)
     * @param string|null $parse Parse mode (Optional)
     * @param mixed $keyboard Keyboard markup (Optional)
     * @param array $extra Additional options (video_start_timestamp, reply_parameters, etc.)
     * @return mixed
     */
    public function CopyMessage($chat, $from_chat, $msgid, $caption = null, $parse = null, $keyboard = null, array $extra = [])
    {
        $data = [
            'chat_id' => $chat,
            'from_chat_id' => $from_chat,
            'message_id' => $msgid,
            'caption' => $caption,
            'parse_mode' => $parse,
            'reply_markup' => $keyboard,
        ];
        return $this->Request('copyMessage', array_merge($data, $extra));
    }

    /**
     * Copies multiple messages of any kind in batch.
     *
     * @param mixed $chat Target ChatID
     * @param mixed $from_chat Source ChatID
     * @param array $message_ids Array of 1-100 message IDs
     * @param array $extra Additional parameters (message_thread_id, disable_notification, protect_content, remove_caption)
     * @return mixed
     */
    public function CopyMessages($chat, $from_chat, array $message_ids, array $extra = [])
    {
        $data = [
            'chat_id' => $chat,
            'from_chat_id' => $from_chat,
            'message_ids' => $message_ids,
        ];
        return $this->Request('copyMessages', array_merge($data, $extra));
    }

    /**
     * Forwards a message of any kind.
     *
     * @param mixed $chat Target ChatID.
     * @param mixed $from Forward Message From ChatID.
     * @param int $msgid Desired Message ID.
     * @param array $extra Additional parameters (video_start_timestamp, message_thread_id, disable_notification, protect_content)
     * @return mixed
     */
    public function ForwardMessage($chat, $from, $msgid, array $extra = [])
    {
        $data = [
            'chat_id' => $chat,
            'from_chat_id' => $from,
            'message_id' => $msgid,
        ];
        return $this->Request('forwardMessage', array_merge($data, $extra));
    }

    /**
     * Forwards multiple messages in batch.
     *
     * @param mixed $chat Target ChatID
     * @param mixed $from Source ChatID
     * @param array $message_ids Array of message IDs
     * @param array $extra Additional parameters
     * @return mixed
     */
    public function ForwardMessages($chat, $from, array $message_ids, array $extra = [])
    {
        $data = [
            'chat_id' => $chat,
            'from_chat_id' => $from,
            'message_ids' => $message_ids,
        ];
        return $this->Request('forwardMessages', array_merge($data, $extra));
    }

    /**
     * Sends a photo.
     *
     * @param mixed $chat Target ChatID.
     * @param mixed $file Photo file_id, URL, or local path.
     * @param string|null $caption Caption text (Optional)
     * @param string|null $parse Parse mode (Optional)
     * @param bool|null $notification Disables notification (Optional)
     * @param int|array|null $reply Reply message ID or reply_parameters array (Optional)
     * @param mixed $keyboard Keyboard markup (Optional)
     * @param array $extra Additional options (has_spoiler, show_caption_above_media, etc.)
     * @return mixed
     */
    public function SendPhoto($chat, $file, $caption = null, $parse = null, $notification = null, $reply = null, $keyboard = null, array $extra = [])
    {
        $data = [
            'chat_id' => $chat,
            'photo' => $file,
            'caption' => $caption,
            'parse_mode' => $parse,
            'disable_notification' => $notification,
            'reply_markup' => $keyboard,
        ];
        if (is_array($reply)) {
            $data['reply_parameters'] = $reply;
        } elseif ($reply !== null) {
            $data['reply_to_message_id'] = $reply;
        }
        return $this->Request('sendPhoto', array_merge($data, $extra));
    }

    /**
     * Sends a video.
     *
     * @param mixed $chat Target ChatID.
     * @param mixed $file Video file_id, URL, or local path.
     * @param string|null $caption Caption text (Optional)
     * @param string|null $parse Parse mode (Optional)
     * @param bool|null $notification Disables notification (Optional)
     * @param int|array|null $reply Reply message ID or reply_parameters array (Optional)
     * @param mixed $keyboard Keyboard markup (Optional)
     * @param array $extra Additional options (cover, start_timestamp, has_spoiler, show_caption_above_media, supports_streaming, etc.)
     * @return mixed
     */
    public function SendVideo($chat, $file, $caption = null, $parse = null, $notification = null, $reply = null, $keyboard = null, array $extra = [])
    {
        $data = [
            'chat_id' => $chat,
            'video' => $file,
            'caption' => $caption,
            'parse_mode' => $parse,
            'disable_notification' => $notification,
            'reply_markup' => $keyboard,
        ];
        if (is_array($reply)) {
            $data['reply_parameters'] = $reply;
        } elseif ($reply !== null) {
            $data['reply_to_message_id'] = $reply;
        }
        return $this->Request('sendVideo', array_merge($data, $extra));
    }

    /**
     * Sends an animation (GIF or H.264/MPEG-4 AVC video without sound).
     *
     * @param mixed $chat Target ChatID
     * @param mixed $file Animation file_id, URL, or local path
     * @param string|null $caption Caption text (Optional)
     * @param string|null $parse Parse mode (Optional)
     * @param int|null $duration Duration in seconds (Optional)
     * @param int|null $width Animation width (Optional)
     * @param int|null $height Animation height (Optional)
     * @param mixed $thumb Thumbnail (Optional)
     * @param bool|null $notification Disables notification (Optional)
     * @param int|array|null $reply Reply message ID or reply_parameters (Optional)
     * @param mixed $keyboard Keyboard markup (Optional)
     * @param array $extra Additional options (has_spoiler, show_caption_above_media, etc.)
     * @return mixed
     */
    public function SendAnimation($chat, $file, $caption = null, $parse = null, $duration = null, $width = null, $height = null, $thumb = null, $notification = null, $reply = null, $keyboard = null, array $extra = [])
    {
        $data = [
            'chat_id' => $chat,
            'animation' => $file,
            'caption' => $caption,
            'parse_mode' => $parse,
            'duration' => $duration,
            'width' => $width,
            'height' => $height,
            'thumbnail' => $thumb,
            'disable_notification' => $notification,
            'reply_markup' => $keyboard,
        ];
        if (is_array($reply)) {
            $data['reply_parameters'] = $reply;
        } elseif ($reply !== null) {
            $data['reply_to_message_id'] = $reply;
        }
        return $this->Request('sendAnimation', array_merge($data, $extra));
    }

    /**
     * Sends an audio file (MP3/M4A).
     *
     * @param mixed $chat Target ChatID.
     * @param mixed $file Audio file_id, URL, or local path.
     * @param string|null $caption Caption text (Optional)
     * @param string|null $parse Parse mode (Optional)
     * @param int|null $duration Duration in seconds (Optional)
     * @param string|null $performer Performer name (Optional)
     * @param string|null $title Track title (Optional)
     * @param mixed $thumb Thumbnail (Optional)
     * @param bool|null $notification Disables notification (Optional)
     * @param int|array|null $reply Reply message ID or reply_parameters (Optional)
     * @param mixed $keyboard Keyboard markup (Optional)
     * @param array $extra Additional options
     * @return mixed
     */
    public function SendAudio($chat, $file, $caption = null, $parse = null, $duration = null, $performer = null, $title = null, $thumb = null, $notification = null, $reply = null, $keyboard = null, array $extra = [])
    {
        $data = [
            'chat_id' => $chat,
            'audio' => $file,
            'caption' => $caption,
            'parse_mode' => $parse,
            'duration' => $duration,
            'title' => $title,
            'performer' => $performer,
            'thumbnail' => $thumb,
            'disable_notification' => $notification,
            'reply_markup' => $keyboard,
        ];
        if (is_array($reply)) {
            $data['reply_parameters'] = $reply;
        } elseif ($reply !== null) {
            $data['reply_to_message_id'] = $reply;
        }
        return $this->Request('sendAudio', array_merge($data, $extra));
    }

    /**
     * Sends a voice message (OGG with OPUS).
     *
     * @param mixed $chat Target ChatID.
     * @param mixed $file Voice file_id, URL, or local path.
     * @param string|null $caption Caption text (Optional)
     * @param string|null $parse Parse mode (Optional)
     * @param int|null $duration Duration in seconds (Optional)
     * @param bool|null $notification Disables notification (Optional)
     * @param int|array|null $reply Reply message ID or reply_parameters (Optional)
     * @param mixed $keyboard Keyboard markup (Optional)
     * @param array $extra Additional options
     * @return mixed
     */
    public function SendVoice($chat, $file, $caption = null, $parse = null, $duration = null, $notification = null, $reply = null, $keyboard = null, array $extra = [])
    {
        $data = [
            'chat_id' => $chat,
            'voice' => $file,
            'caption' => $caption,
            'parse_mode' => $parse,
            'duration' => $duration,
            'disable_notification' => $notification,
            'reply_markup' => $keyboard,
        ];
        if (is_array($reply)) {
            $data['reply_parameters'] = $reply;
        } elseif ($reply !== null) {
            $data['reply_to_message_id'] = $reply;
        }
        return $this->Request('sendVoice', array_merge($data, $extra));
    }

    /**
     * Sends a round video message (video note).
     *
     * @param mixed $chat Target ChatID
     * @param mixed $file Video note file_id, URL, or local path
     * @param int|null $duration Duration in seconds (Optional)
     * @param int|null $length Video note diameter (Optional)
     * @param mixed $thumb Thumbnail (Optional)
     * @param bool|null $notification Disables notification (Optional)
     * @param int|array|null $reply Reply message ID or reply_parameters (Optional)
     * @param mixed $keyboard Keyboard markup (Optional)
     * @param array $extra Additional options
     * @return mixed
     */
    public function SendVideoNote($chat, $file, $duration = null, $length = null, $thumb = null, $notification = null, $reply = null, $keyboard = null, array $extra = [])
    {
        $data = [
            'chat_id' => $chat,
            'video_note' => $file,
            'duration' => $duration,
            'length' => $length,
            'thumbnail' => $thumb,
            'disable_notification' => $notification,
            'reply_markup' => $keyboard,
        ];
        if (is_array($reply)) {
            $data['reply_parameters'] = $reply;
        } elseif ($reply !== null) {
            $data['reply_to_message_id'] = $reply;
        }
        return $this->Request('sendVideoNote', array_merge($data, $extra));
    }

    /**
     * Sends a general file / document.
     *
     * @param mixed $chat Target ChatID.
     * @param mixed $file Document file_id, URL, or local path.
     * @param string|null $caption Caption text (Optional)
     * @param string|null $parse Parse mode (Optional)
     * @param mixed $thumb Thumbnail (Optional)
     * @param bool|null $notification Disables notification (Optional)
     * @param int|array|null $reply Reply message ID or reply_parameters (Optional)
     * @param mixed $keyboard Keyboard markup (Optional)
     * @param array $extra Additional options
     * @return mixed
     */
    public function SendDocument($chat, $file, $caption = null, $parse = null, $thumb = null, $notification = null, $reply = null, $keyboard = null, array $extra = [])
    {
        $data = [
            'chat_id' => $chat,
            'document' => $file,
            'thumbnail' => $thumb,
            'caption' => $caption,
            'parse_mode' => $parse,
            'disable_notification' => $notification,
            'reply_markup' => $keyboard,
        ];
        if (is_array($reply)) {
            $data['reply_parameters'] = $reply;
        } elseif ($reply !== null) {
            $data['reply_to_message_id'] = $reply;
        }
        return $this->Request('sendDocument', array_merge($data, $extra));
    }

    /**
     * Sends paid media (Telegram Stars).
     *
     * @param mixed $chat Target ChatID
     * @param int $star_count Number of Telegram Stars that must be paid
     * @param array $media Array of InputPaidMedia objects
     * @param string|null $caption Caption text (Optional)
     * @param string|null $parse Parse mode (Optional)
     * @param array $extra Additional options (payload, show_caption_above_media, reply_parameters)
     * @return mixed
     */
    public function SendPaidMedia($chat, $star_count, array $media, $caption = null, $parse = null, array $extra = [])
    {
        $data = [
            'chat_id' => $chat,
            'star_count' => $star_count,
            'media' => $media,
            'caption' => $caption,
            'parse_mode' => $parse,
        ];
        return $this->Request('sendPaidMedia', array_merge($data, $extra));
    }

    /**
     * Sends a group of photos, videos, documents, or audios as an album.
     *
     * @param mixed $chat Target ChatID.
     * @param array $media Array of InputMedia objects.
     * @param array $extra Additional parameters (business_connection_id, message_thread_id, disable_notification, protect_content, reply_parameters)
     * @return mixed
     */
    public function SendMediaGroup($chat, $media, array $extra = [])
    {
        $data = [
            'chat_id' => $chat,
            'media' => is_string($media) ? $media : json_encode($media),
        ];
        return $this->Request('sendMediaGroup', array_merge($data, $extra));
    }

    /**
     * Sends a point on the map.
     *
     * @param mixed $chat Target ChatID
     * @param float $latitude Latitude
     * @param float $longitude Longitude
     * @param int|null $live_period Live period in seconds (Optional)
     * @param float|null $horizontal_accuracy Accuracy in meters (Optional)
     * @param int|null $heading Direction heading in degrees 1-360 (Optional)
     * @param int|null $proximity_alert_radius Proximity alert radius (Optional)
     * @param bool|null $notification Disables notification (Optional)
     * @param int|array|null $reply Reply message ID or reply_parameters (Optional)
     * @param mixed $keyboard Keyboard markup (Optional)
     * @param array $extra Additional options
     * @return mixed
     */
    public function SendLocation($chat, $latitude, $longitude, $live_period = null, $horizontal_accuracy = null, $heading = null, $proximity_alert_radius = null, $notification = null, $reply = null, $keyboard = null, array $extra = [])
    {
        $data = [
            'chat_id' => $chat,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'live_period' => $live_period,
            'horizontal_accuracy' => $horizontal_accuracy,
            'heading' => $heading,
            'proximity_alert_radius' => $proximity_alert_radius,
            'disable_notification' => $notification,
            'reply_markup' => $keyboard,
        ];
        if (is_array($reply)) {
            $data['reply_parameters'] = $reply;
        } elseif ($reply !== null) {
            $data['reply_to_message_id'] = $reply;
        }
        return $this->Request('sendLocation', array_merge($data, $extra));
    }

    /**
     * Edits live location message.
     *
     * @param mixed $chat
     * @param int $msgid
     * @param float $latitude
     * @param float $longitude
     * @param mixed $keyboard
     * @param array $extra
     * @return mixed
     */
    public function EditMessageLiveLocation($chat, $msgid, $latitude, $longitude, $keyboard = null, array $extra = [])
    {
        $data = [
            'chat_id' => $chat,
            'message_id' => $msgid,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'reply_markup' => $keyboard,
        ];
        return $this->Request('editMessageLiveLocation', array_merge($data, $extra));
    }

    /**
     * Stops updating a live location message.
     *
     * @param mixed $chat
     * @param int $msgid
     * @param mixed $keyboard
     * @param array $extra
     * @return mixed
     */
    public function StopMessageLiveLocation($chat, $msgid, $keyboard = null, array $extra = [])
    {
        $data = [
            'chat_id' => $chat,
            'message_id' => $msgid,
            'reply_markup' => $keyboard,
        ];
        return $this->Request('stopMessageLiveLocation', array_merge($data, $extra));
    }

    /**
     * Sends information about a venue.
     *
     * @param mixed $chat
     * @param float $latitude
     * @param float $longitude
     * @param string $title
     * @param string $address
     * @param string|null $foursquare_id
     * @param string|null $foursquare_type
     * @param bool|null $notification
     * @param int|array|null $reply
     * @param mixed $keyboard
     * @param array $extra
     * @return mixed
     */
    public function SendVenue($chat, $latitude, $longitude, $title, $address, $foursquare_id = null, $foursquare_type = null, $notification = null, $reply = null, $keyboard = null, array $extra = [])
    {
        $data = [
            'chat_id' => $chat,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'title' => $title,
            'address' => $address,
            'foursquare_id' => $foursquare_id,
            'foursquare_type' => $foursquare_type,
            'disable_notification' => $notification,
            'reply_markup' => $keyboard,
        ];
        if (is_array($reply)) {
            $data['reply_parameters'] = $reply;
        } elseif ($reply !== null) {
            $data['reply_to_message_id'] = $reply;
        }
        return $this->Request('sendVenue', array_merge($data, $extra));
    }

    /**
     * Sends a phone contact.
     *
     * @param mixed $chat
     * @param string $phone
     * @param string $first_name
     * @param string|null $last_name
     * @param string|null $vcard
     * @param bool|null $notification
     * @param int|array|null $reply
     * @param mixed $keyboard
     * @param array $extra
     * @return mixed
     */
    public function SendContact($chat, $phone, $first_name, $last_name = null, $vcard = null, $notification = null, $reply = null, $keyboard = null, array $extra = [])
    {
        $data = [
            'chat_id' => $chat,
            'phone_number' => $phone,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'vcard' => $vcard,
            'disable_notification' => $notification,
            'reply_markup' => $keyboard,
        ];
        if (is_array($reply)) {
            $data['reply_parameters'] = $reply;
        } elseif ($reply !== null) {
            $data['reply_to_message_id'] = $reply;
        }
        return $this->Request('sendContact', array_merge($data, $extra));
    }

    /**
     * Sends a poll.
     *
     * @param mixed $chat
     * @param string $question
     * @param array $options Array of answer strings or InputPollOption objects
     * @param array $extra Additional poll parameters (is_anonymous, type, allows_multiple_answers, correct_option_id, explanation, open_period, close_date, is_closed)
     * @return mixed
     */
    public function SendPoll($chat, $question, array $options, array $extra = [])
    {
        $data = [
            'chat_id' => $chat,
            'question' => $question,
            'options' => $options,
        ];
        return $this->Request('sendPoll', array_merge($data, $extra));
    }

    /**
     * Stops a poll which was sent by the bot.
     *
     * @param mixed $chat
     * @param int $msgid
     * @param mixed $keyboard
     * @return mixed
     */
    public function StopPoll($chat, $msgid, $keyboard = null)
    {
        return $this->Request('stopPoll', [
            'chat_id' => $chat,
            'message_id' => $msgid,
            'reply_markup' => $keyboard,
        ]);
    }

    /**
     * Sends an animated emoji that will display a random value.
     *
     * @param mixed $chat
     * @param string $emoji Dice emoji: 🎲, 🎯, 🏀, ⚽, 🎳, or 🎰
     * @param bool|null $notification
     * @param int|array|null $reply
     * @param mixed $keyboard
     * @param array $extra
     * @return mixed
     */
    public function SendDice($chat, $emoji = '🎲', $notification = null, $reply = null, $keyboard = null, array $extra = [])
    {
        $data = [
            'chat_id' => $chat,
            'emoji' => $emoji,
            'disable_notification' => $notification,
            'reply_markup' => $keyboard,
        ];
        if (is_array($reply)) {
            $data['reply_parameters'] = $reply;
        } elseif ($reply !== null) {
            $data['reply_to_message_id'] = $reply;
        }
        return $this->Request('sendDice', array_merge($data, $extra));
    }

    /**
     * Sends a sticker (.WEBP, .TGS, .WEBM).
     *
     * @param mixed $chat Target ChatID
     * @param mixed $sticker Sticker file_id, URL, or local path
     * @param string|null $emoji Emoji associated with the sticker (Optional)
     * @param bool|null $notification (Optional)
     * @param int|array|null $reply (Optional)
     * @param mixed $keyboard (Optional)
     * @param array $extra
     * @return mixed
     */
    public function SendSticker($chat, $sticker, $emoji = null, $notification = null, $reply = null, $keyboard = null, array $extra = [])
    {
        $data = [
            'chat_id' => $chat,
            'sticker' => $sticker,
            'emoji' => $emoji,
            'disable_notification' => $notification,
            'reply_markup' => $keyboard,
        ];
        if (is_array($reply)) {
            $data['reply_parameters'] = $reply;
        } elseif ($reply !== null) {
            $data['reply_to_message_id'] = $reply;
        }
        return $this->Request('sendSticker', array_merge($data, $extra));
    }

    /**
     * Broadcasts a chat action to indicate that the bot is performing work.
     *
     * @param mixed $chat Target ChatID.
     * @param string $action typing, upload_photo, record_video, upload_video, record_voice, upload_voice, upload_document, choose_sticker, find_location, record_video_note, upload_video_note
     * @param array $extra (message_thread_id, business_connection_id)
     * @return mixed
     */
    public function SendChatAction($chat, $action, array $extra = [])
    {
        $data = [
            'chat_id' => $chat,
            'action' => $action,
        ];
        return $this->Request('sendChatAction', array_merge($data, $extra));
    }

    /**
     * Changes the chosen reactions on a message (Bot API 7.0+).
     *
     * @param mixed $chat Target ChatID
     * @param int $msgid Target Message ID
     * @param array|string $reaction Reaction emoji string or array of ReactionType objects (e.g. [['type'=>'emoji', 'emoji'=>'👍']])
     * @param bool $is_big Pass True to make the reaction big and animated
     * @return mixed
     */
    public function SetMessageReaction($chat, $msgid, $reaction = [], $is_big = false)
    {
        $formattedReaction = [];
        if (is_string($reaction)) {
            $formattedReaction = [['type' => 'emoji', 'emoji' => $reaction]];
        } elseif (is_array($reaction)) {
            if (!empty($reaction) && !isset($reaction[0])) {
                $formattedReaction = [$reaction];
            } else {
                $formattedReaction = $reaction;
            }
        }

        return $this->Request('setMessageReaction', [
            'chat_id' => $chat,
            'message_id' => $msgid,
            'reaction' => $formattedReaction,
            'is_big' => (bool)$is_big,
        ]);
    }

    // =========================================================================
    // EDITING & DELETING MESSAGES
    // =========================================================================

    /**
     * Edits text and game messages.
     *
     * @param mixed $chat Target ChatID.
     * @param int $msgid ID of Message.
     * @param string $text New text of the message.
     * @param string|null $parse HTML, Markdown, or MarkdownV2 (Optional)
     * @param mixed $keyboard New inline keyboard (Optional)
     * @param array $extra Additional options (link_preview_options, etc.)
     * @return mixed
     */
    public function EditMessage($chat, $msgid, $text, $parse = null, $keyboard = null, array $extra = [])
    {
        $data = [
            'chat_id' => $chat,
            'message_id' => $msgid,
            'text' => $text,
            'parse_mode' => $parse,
            'reply_markup' => $keyboard,
        ];
        return $this->Request('editMessageText', array_merge($data, $extra));
    }

    /**
     * Edits caption of messages.
     *
     * @param mixed $chat Target ChatID.
     * @param int $msgid ID of Message.
     * @param string|null $caption New caption.
     * @param string|null $parse HTML, Markdown, or MarkdownV2 (Optional)
     * @param mixed $keyboard New inline keyboard (Optional)
     * @param array $extra Additional options (show_caption_above_media, etc.)
     * @return mixed
     */
    public function EditMessageCaption($chat, $msgid, $caption = null, $parse = null, $keyboard = null, array $extra = [])
    {
        $data = [
            'chat_id' => $chat,
            'message_id' => $msgid,
            'caption' => $caption,
            'parse_mode' => $parse,
            'reply_markup' => $keyboard,
        ];
        return $this->Request('editMessageCaption', array_merge($data, $extra));
    }

    /**
     * Edits animation, audio, document, photo, or video messages.
     *
     * @param mixed $chat
     * @param int $msgid
     * @param array $media InputMedia object
     * @param mixed $keyboard
     * @param array $extra
     * @return mixed
     */
    public function EditMessageMedia($chat, $msgid, $media, $keyboard = null, array $extra = [])
    {
        $data = [
            'chat_id' => $chat,
            'message_id' => $msgid,
            'media' => $media,
            'reply_markup' => $keyboard,
        ];
        return $this->Request('editMessageMedia', array_merge($data, $extra));
    }

    /**
     * Edits only the reply markup of messages.
     *
     * @param mixed $chat
     * @param int $msgid
     * @param mixed $keyboard
     * @param array $extra
     * @return mixed
     */
    public function EditMessageReplyMarkup($chat, $msgid, $keyboard = null, array $extra = [])
    {
        $data = [
            'chat_id' => $chat,
            'message_id' => $msgid,
            'reply_markup' => $keyboard,
        ];
        return $this->Request('editMessageReplyMarkup', array_merge($data, $extra));
    }

    /**
     * Deletes a message.
     *
     * @param mixed $chat Target ChatID.
     * @param int $msgid ID of Message.
     * @return mixed
     */
    public function DeleteMessage($chat, $msgid)
    {
        return $this->Request('deleteMessage', [
            'chat_id' => $chat,
            'message_id' => $msgid,
        ]);
    }

    /**
     * Deletes multiple messages simultaneously in batch (Bot API 7.0+).
     *
     * @param mixed $chat Target ChatID
     * @param array $message_ids Array of 1-100 message IDs to delete
     * @return mixed
     */
    public function DeleteMessages($chat, array $message_ids)
    {
        return $this->Request('deleteMessages', [
            'chat_id' => $chat,
            'message_ids' => $message_ids,
        ]);
    }

    // =========================================================================
    // CHAT MANAGEMENT & MODERATION
    // =========================================================================

    /**
     * Bans a user in a group, supergroup or channel.
     *
     * @param mixed $chat Target ChatID
     * @param int $user_id User ID to ban
     * @param int $until_date Date when the user will be unbanned; 0 for permanent
     * @param bool $revoke_messages Pass true to delete all messages from the chat for this user
     * @return mixed
     */
    public function BanChatMember($chat, $user_id, $until_date = 0, $revoke_messages = false)
    {
        return $this->Request('banChatMember', [
            'chat_id' => $chat,
            'user_id' => $user_id,
            'until_date' => $until_date,
            'revoke_messages' => (bool)$revoke_messages,
        ]);
    }

    /**
     * Unbans a previously banned user in a supergroup or channel.
     *
     * @param mixed $chat Target ChatID
     * @param int $user_id User ID to unban
     * @param bool $only_if_banned Do nothing if the user is not banned
     * @return mixed
     */
    public function UnbanChatMember($chat, $user_id, $only_if_banned = false)
    {
        return $this->Request('unbanChatMember', [
            'chat_id' => $chat,
            'user_id' => $user_id,
            'only_if_banned' => (bool)$only_if_banned,
        ]);
    }

    /**
     * Restricts a user in a supergroup.
     *
     * @param mixed $chat Target ChatID
     * @param int $user_id Target User ID
     * @param array $permissions ChatPermissions object
     * @param int $until_date Date when restrictions will be lifted; 0 for permanent
     * @param array $extra Additional options (use_independent_chat_permissions)
     * @return mixed
     */
    public function RestrictChatMember($chat, $user_id, array $permissions, $until_date = 0, array $extra = [])
    {
        $data = [
            'chat_id' => $chat,
            'user_id' => $user_id,
            'permissions' => $permissions,
            'until_date' => $until_date,
        ];
        return $this->Request('restrictChatMember', array_merge($data, $extra));
    }

    /**
     * Promotes or demotes a user in a supergroup or channel.
     *
     * @param mixed $chat Target ChatID
     * @param int $user_id Target User ID
     * @param array $rights Associative array of administrator rights
     * @return mixed
     */
    public function PromoteChatMember($chat, $user_id, array $rights = [])
    {
        $data = array_merge([
            'chat_id' => $chat,
            'user_id' => $user_id,
        ], $rights);
        return $this->Request('promoteChatMember', $data);
    }

    /**
     * Sets a custom title for an administrator in a supergroup promoted by the bot.
     *
     * @param mixed $chat
     * @param int $user_id
     * @param string $custom_title 0-16 characters
     * @return mixed
     */
    public function SetChatAdministratorCustomTitle($chat, $user_id, $custom_title)
    {
        return $this->Request('setChatAdministratorCustomTitle', [
            'chat_id' => $chat,
            'user_id' => $user_id,
            'custom_title' => $custom_title,
        ]);
    }

    /**
     * Bans a channel chat in a supergroup or channel.
     *
     * @param mixed $chat
     * @param int $sender_chat_id
     * @return mixed
     */
    public function BanChatSenderChat($chat, $sender_chat_id)
    {
        return $this->Request('banChatSenderChat', [
            'chat_id' => $chat,
            'sender_chat_id' => $sender_chat_id,
        ]);
    }

    /**
     * Unbans a previously banned channel chat in a supergroup or channel.
     *
     * @param mixed $chat
     * @param int $sender_chat_id
     * @return mixed
     */
    public function UnbanChatSenderChat($chat, $sender_chat_id)
    {
        return $this->Request('unbanChatSenderChat', [
            'chat_id' => $chat,
            'sender_chat_id' => $sender_chat_id,
        ]);
    }

    /**
     * Sets default chat permissions for all members.
     *
     * @param mixed $chat
     * @param array $permissions ChatPermissions object
     * @param array $extra (use_independent_chat_permissions)
     * @return mixed
     */
    public function SetChatPermissions($chat, array $permissions, array $extra = [])
    {
        $data = [
            'chat_id' => $chat,
            'permissions' => $permissions,
        ];
        return $this->Request('setChatPermissions', array_merge($data, $extra));
    }

    /**
     * Generates a new primary invite link for a chat.
     *
     * @param mixed $chat
     * @return mixed
     */
    public function ExportChatInviteLink($chat)
    {
        return $this->Request('exportChatInviteLink', ['chat_id' => $chat]);
    }

    /**
     * Creates an additional invite link for a chat.
     *
     * @param mixed $chat
     * @param array $options (name, expire_date, member_limit, creates_join_request)
     * @return mixed
     */
    public function CreateChatInviteLink($chat, array $options = [])
    {
        $data = array_merge(['chat_id' => $chat], $options);
        return $this->Request('createChatInviteLink', $data);
    }

    /**
     * Edits an existing non-primary invite link.
     *
     * @param mixed $chat
     * @param string $invite_link
     * @param array $options (name, expire_date, member_limit, creates_join_request)
     * @return mixed
     */
    public function EditChatInviteLink($chat, $invite_link, array $options = [])
    {
        $data = array_merge([
            'chat_id' => $chat,
            'invite_link' => $invite_link,
        ], $options);
        return $this->Request('editChatInviteLink', $data);
    }

    /**
     * Revokes an invite link.
     *
     * @param mixed $chat
     * @param string $invite_link
     * @return mixed
     */
    public function RevokeChatInviteLink($chat, $invite_link)
    {
        return $this->Request('revokeChatInviteLink', [
            'chat_id' => $chat,
            'invite_link' => $invite_link,
        ]);
    }

    /**
     * Approves a chat join request.
     *
     * @param mixed $chat
     * @param int $user_id
     * @return mixed
     */
    public function ApproveChatJoinRequest($chat, $user_id)
    {
        return $this->Request('approveChatJoinRequest', [
            'chat_id' => $chat,
            'user_id' => $user_id,
        ]);
    }

    /**
     * Declines a chat join request.
     *
     * @param mixed $chat
     * @param int $user_id
     * @return mixed
     */
    public function DeclineChatJoinRequest($chat, $user_id)
    {
        return $this->Request('declineChatJoinRequest', [
            'chat_id' => $chat,
            'user_id' => $user_id,
        ]);
    }

    /**
     * Sets a new profile photo for the chat.
     *
     * @param mixed $chat
     * @param mixed $photo File or path
     * @return mixed
     */
    public function SetChatPhoto($chat, $photo)
    {
        return $this->Request('setChatPhoto', [
            'chat_id' => $chat,
            'photo' => $photo,
        ]);
    }

    /**
     * Deletes a chat profile photo.
     *
     * @param mixed $chat
     * @return mixed
     */
    public function DeleteChatPhoto($chat)
    {
        return $this->Request('deleteChatPhoto', ['chat_id' => $chat]);
    }

    /**
     * Changes the title of a chat.
     *
     * @param mixed $chat
     * @param string $title 1-128 characters
     * @return mixed
     */
    public function SetChatTitle($chat, $title)
    {
        return $this->Request('setChatTitle', [
            'chat_id' => $chat,
            'title' => $title,
        ]);
    }

    /**
     * Changes the description of a chat.
     *
     * @param mixed $chat
     * @param string|null $description 0-255 characters
     * @return mixed
     */
    public function SetChatDescription($chat, $description = null)
    {
        return $this->Request('setChatDescription', [
            'chat_id' => $chat,
            'description' => $description,
        ]);
    }

    /**
     * Pins a message in a chat.
     *
     * @param mixed $chat Where You Want To Pin Message.
     * @param int $msgid Identifier Of A Message To Pin.
     * @param bool $notification Disables notification.
     * @param array $extra (business_connection_id)
     * @return mixed
     */
    public function PinMessage($chat, $msgid, $notification = false, array $extra = [])
    {
        $data = [
            'chat_id' => $chat,
            'message_id' => $msgid,
            'disable_notification' => (bool)$notification,
        ];
        return $this->Request('pinChatMessage', array_merge($data, $extra));
    }

    /**
     * Removes a pinned message in chat.
     *
     * @param mixed $chat Where You Want To Remove Pinned Message.
     * @param int $msgid Identifier Of Message To Unpin.
     * @param array $extra (business_connection_id)
     * @return mixed
     */
    public function UnPinMessage($chat, $msgid, array $extra = [])
    {
        $data = [
            'chat_id' => $chat,
            'message_id' => $msgid,
        ];
        return $this->Request('unpinChatMessage', array_merge($data, $extra));
    }

    /**
     * Removes all pinned messages in chat.
     *
     * @param mixed $chat Where You Want To Remove Pinned Messages.
     * @return mixed
     */
    public function UnPinAllChatMessages($chat)
    {
        return $this->Request('unpinAllChatMessages', ['chat_id' => $chat]);
    }

    /**
     * Leaves a group, supergroup or channel.
     *
     * @param mixed $chat
     * @return mixed
     */
    public function LeaveChat($chat)
    {
        return $this->Request('leaveChat', ['chat_id' => $chat]);
    }

    /**
     * Gets up to date information about the chat (ChatFullInfo object).
     *
     * @param mixed $chat
     * @return mixed
     */
    public function GetChat($chat)
    {
        return $this->Request('getChat', ['chat_id' => $chat]);
    }

    /**
     * Gets a list of administrators in a chat.
     *
     * @param mixed $chat
     * @return mixed
     */
    public function GetChatAdministrators($chat)
    {
        return $this->Request('getChatAdministrators', ['chat_id' => $chat]);
    }

    /**
     * Gets the number of members in a chat.
     *
     * @param mixed $chat
     * @return mixed
     */
    public function GetChatMemberCount($chat)
    {
        return $this->Request('getChatMemberCount', ['chat_id' => $chat]);
    }

    /**
     * Returns member info or membership status in a chat.
     *
     * @param mixed $chatid Chat ID or @username
     * @param mixed $userid User ID
     * @return mixed Returns member status string or ChatMember object
     */
    public function GetChatMember($chatid, $userid)
    {
        $res = $this->Request('getChatMember', [
            'chat_id' => $chatid,
            'user_id' => $userid,
        ]);

        if (is_object($res) && isset($res->result->status)) {
            return $res->result->status;
        }
        if (is_array($res) && isset($res['result']['status'])) {
            return $res['result']['status'];
        }

        return $res;
    }

    /**
     * Sets the bot's menu button in a private chat, or the default menu button.
     *
     * @param mixed|null $chat
     * @param array|null $menu_button MenuButton object (e.g. ['type' => 'default'])
     * @return mixed
     */
    public function SetChatMenuButton($chat = null, array $menu_button = null)
    {
        $data = [];
        if ($chat !== null) {
            $data['chat_id'] = $chat;
        }
        if ($menu_button !== null) {
            $data['menu_button'] = $menu_button;
        }
        return $this->Request('setChatMenuButton', $data);
    }

    /**
     * Gets current value of the bot's menu button.
     *
     * @param mixed|null $chat
     * @return mixed
     */
    public function GetChatMenuButton($chat = null)
    {
        $data = [];
        if ($chat !== null) {
            $data['chat_id'] = $chat;
        }
        return $this->Request('getChatMenuButton', $data);
    }

    // =========================================================================
    // FORUM TOPICS (SUPERGROUPS)
    // =========================================================================

    /**
     * Creates a topic in a forum supergroup chat.
     *
     * @param mixed $chat
     * @param string $name Topic name (1-128 characters)
     * @param int|null $icon_color Color in RGB format (Optional)
     * @param string|null $icon_custom_emoji_id Unique identifier of custom emoji (Optional)
     * @return mixed
     */
    public function CreateForumTopic($chat, $name, $icon_color = null, $icon_custom_emoji_id = null)
    {
        $data = [
            'chat_id' => $chat,
            'name' => $name,
            'icon_color' => $icon_color,
            'icon_custom_emoji_id' => $icon_custom_emoji_id,
        ];
        return $this->Request('createForumTopic', $data);
    }

    /**
     * Edits name and icon of a topic in a forum supergroup chat.
     *
     * @param mixed $chat
     * @param int $message_thread_id
     * @param string|null $name
     * @param string|null $icon_custom_emoji_id
     * @return mixed
     */
    public function EditForumTopic($chat, $message_thread_id, $name = null, $icon_custom_emoji_id = null)
    {
        $data = [
            'chat_id' => $chat,
            'message_thread_id' => $message_thread_id,
            'name' => $name,
            'icon_custom_emoji_id' => $icon_custom_emoji_id,
        ];
        return $this->Request('editForumTopic', $data);
    }

    /**
     * Closes an open topic in a forum supergroup chat.
     *
     * @param mixed $chat
     * @param int $message_thread_id
     * @return mixed
     */
    public function CloseForumTopic($chat, $message_thread_id)
    {
        return $this->Request('closeForumTopic', [
            'chat_id' => $chat,
            'message_thread_id' => $message_thread_id,
        ]);
    }

    /**
     * Reopens a closed topic in a forum supergroup chat.
     *
     * @param mixed $chat
     * @param int $message_thread_id
     * @return mixed
     */
    public function ReopenForumTopic($chat, $message_thread_id)
    {
        return $this->Request('reopenForumTopic', [
            'chat_id' => $chat,
            'message_thread_id' => $message_thread_id,
        ]);
    }

    /**
     * Deletes a forum topic along with all its messages in a forum supergroup chat.
     *
     * @param mixed $chat
     * @param int $message_thread_id
     * @return mixed
     */
    public function DeleteForumTopic($chat, $message_thread_id)
    {
        return $this->Request('deleteForumTopic', [
            'chat_id' => $chat,
            'message_thread_id' => $message_thread_id,
        ]);
    }

    /**
     * Clears the list of pinned messages in a forum topic.
     *
     * @param mixed $chat
     * @param int $message_thread_id
     * @return mixed
     */
    public function UnpinAllForumTopicMessages($chat, $message_thread_id)
    {
        return $this->Request('unpinAllForumTopicMessages', [
            'chat_id' => $chat,
            'message_thread_id' => $message_thread_id,
        ]);
    }

    /**
     * Edits the name of the 'General' topic in a forum supergroup chat.
     *
     * @param mixed $chat
     * @param string $name 1-128 characters
     * @return mixed
     */
    public function EditGeneralForumTopic($chat, $name)
    {
        return $this->Request('editGeneralForumTopic', [
            'chat_id' => $chat,
            'name' => $name,
        ]);
    }

    /**
     * Closes the 'General' topic in a forum supergroup chat.
     *
     * @param mixed $chat
     * @return mixed
     */
    public function CloseGeneralForumTopic($chat)
    {
        return $this->Request('closeGeneralForumTopic', ['chat_id' => $chat]);
    }

    /**
     * Reopens the closed 'General' topic in a forum supergroup chat.
     *
     * @param mixed $chat
     * @return mixed
     */
    public function ReopenGeneralForumTopic($chat)
    {
        return $this->Request('reopenGeneralForumTopic', ['chat_id' => $chat]);
    }

    /**
     * Hides the 'General' topic in a forum supergroup chat.
     *
     * @param mixed $chat
     * @return mixed
     */
    public function HideGeneralForumTopic($chat)
    {
        return $this->Request('hideGeneralForumTopic', ['chat_id' => $chat]);
    }

    /**
     * Unhides the 'General' topic in a forum supergroup chat.
     *
     * @param mixed $chat
     * @return mixed
     */
    public function UnhideGeneralForumTopic($chat)
    {
        return $this->Request('unhideGeneralForumTopic', ['chat_id' => $chat]);
    }

    // =========================================================================
    // VERIFICATION MANAGEMENT (BOT API 8.2+)
    // =========================================================================

    /**
     * Verifies a user on behalf of the organization which is represented by the bot.
     *
     * @param int $user_id
     * @param string|null $custom_description Description 0-70 characters
     * @return mixed
     */
    public function VerifyUser($user_id, $custom_description = null)
    {
        $data = ['user_id' => $user_id];
        if ($custom_description !== null) {
            $data['custom_description'] = $custom_description;
        }
        return $this->Request('verifyUser', $data);
    }

    /**
     * Verifies a chat on behalf of the organization which is represented by the bot.
     *
     * @param mixed $chat
     * @param string|null $custom_description Description 0-70 characters
     * @return mixed
     */
    public function VerifyChat($chat, $custom_description = null)
    {
        $data = ['chat_id' => $chat];
        if ($custom_description !== null) {
            $data['custom_description'] = $custom_description;
        }
        return $this->Request('verifyChat', $data);
    }

    /**
     * Removes verification from a user who is currently verified on behalf of the organization.
     *
     * @param int $user_id
     * @return mixed
     */
    public function RemoveUserVerification($user_id)
    {
        return $this->Request('removeUserVerification', ['user_id' => $user_id]);
    }

    /**
     * Removes verification from a chat that is currently verified on behalf of the organization.
     *
     * @param mixed $chat
     * @return mixed
     */
    public function RemoveChatVerification($chat)
    {
        return $this->Request('removeChatVerification', ['chat_id' => $chat]);
    }

    // =========================================================================
    // GIFTS, STARS & PAYMENTS (BOT API 7.4 - 8.3+)
    // =========================================================================

    /**
     * Sends a gift to a given user or channel chat (Bot API 8.2 - 8.3+).
     *
     * @param int $user_id Unique identifier of the target user
     * @param string $gift_id Identifier of the gift
     * @param array $options Additional options (chat_id, pay_for_upgrade, text, text_parse_mode, text_entities)
     * @return mixed
     */
    public function SendGift($user_id, $gift_id, array $options = [])
    {
        $data = array_merge([
            'user_id' => $user_id,
            'gift_id' => $gift_id,
        ], $options);
        return $this->Request('sendGift', $data);
    }

    /**
     * Returns the list of gifts that can be sent by the bot to users.
     *
     * @return mixed
     */
    public function GetAvailableGifts()
    {
        return $this->Request('getAvailableGifts');
    }

    /**
     * Returns the bot's Telegram Star transactions in chronological order.
     *
     * @param array $options (offset, limit)
     * @return mixed
     */
    public function GetStarTransactions(array $options = [])
    {
        return $this->Request('getStarTransactions', $options);
    }

    /**
     * Refunds a successful payment in Telegram Stars.
     *
     * @param int $user_id Identifier of the user who made the payment
     * @param string $telegram_payment_charge_id Telegram payment identifier
     * @return mixed
     */
    public function RefundStarPayment($user_id, $telegram_payment_charge_id)
    {
        return $this->Request('refundStarPayment', [
            'user_id' => $user_id,
            'telegram_payment_charge_id' => $telegram_payment_charge_id,
        ]);
    }

    /**
     * Allows the bot to cancel or re-enable a Telegram Star subscription.
     *
     * @param int $user_id Identifier of the user
     * @param string $telegram_payment_charge_id Payment identifier for the subscription
     * @param bool $is_canceled Pass True to cancel the subscription
     * @return mixed
     */
    public function EditUserStarSubscription($user_id, $telegram_payment_charge_id, $is_canceled)
    {
        return $this->Request('editUserStarSubscription', [
            'user_id' => $user_id,
            'telegram_payment_charge_id' => $telegram_payment_charge_id,
            'is_canceled' => (bool)$is_canceled,
        ]);
    }

    // =========================================================================
    // BOT CONFIGURATION & INFO
    // =========================================================================

    /**
     * Tests bot token and returns basic information about the bot (User object).
     *
     * @return mixed
     */
    public function GetMe()
    {
        return $this->Request('getMe');
    }

    /**
     * Logs out from the cloud Bot API server before launching the bot locally.
     *
     * @return mixed
     */
    public function LogOut()
    {
        return $this->Request('logOut');
    }

    /**
     * Closes the bot instance before moving it between local Bot API servers.
     *
     * @return mixed
     */
    public function CloseBot()
    {
        return $this->Request('close');
    }

    /**
     * Gets the current list of the bot's commands.
     *
     * @param array $options (scope, language_code)
     * @return mixed
     */
    public function GetMyCommands(array $options = [])
    {
        return $this->Request('getMyCommands', $options);
    }

    /**
     * Changes the list of the bot's commands.
     *
     * @param array $commands Array of BotCommand objects [['command'=>'start', 'description'=>'Start bot']]
     * @param array $options (scope, language_code)
     * @return mixed
     */
    public function SetMyCommands(array $commands, array $options = [])
    {
        $data = array_merge(['commands' => $commands], $options);
        return $this->Request('setMyCommands', $data);
    }

    /**
     * Deletes the list of the bot's commands for the given scope and user language.
     *
     * @param array $options (scope, language_code)
     * @return mixed
     */
    public function DeleteMyCommands(array $options = [])
    {
        return $this->Request('deleteMyCommands', $options);
    }

    /**
     * Changes the bot's name.
     *
     * @param string|null $name 0-64 characters
     * @param string|null $language_code
     * @return mixed
     */
    public function SetMyName($name = null, $language_code = null)
    {
        $data = [];
        if ($name !== null) {
            $data['name'] = $name;
        }
        if ($language_code !== null) {
            $data['language_code'] = $language_code;
        }
        return $this->Request('setMyName', $data);
    }

    /**
     * Gets the current bot name for the given user language.
     *
     * @param string|null $language_code
     * @return mixed
     */
    public function GetMyName($language_code = null)
    {
        $data = [];
        if ($language_code !== null) {
            $data['language_code'] = $language_code;
        }
        return $this->Request('getMyName', $data);
    }

    /**
     * Changes the bot's description.
     *
     * @param string|null $description 0-512 characters
     * @param string|null $language_code
     * @return mixed
     */
    public function SetMyDescription($description = null, $language_code = null)
    {
        $data = [];
        if ($description !== null) {
            $data['description'] = $description;
        }
        if ($language_code !== null) {
            $data['language_code'] = $language_code;
        }
        return $this->Request('setMyDescription', $data);
    }

    /**
     * Gets the current bot description.
     *
     * @param string|null $language_code
     * @return mixed
     */
    public function GetMyDescription($language_code = null)
    {
        $data = [];
        if ($language_code !== null) {
            $data['language_code'] = $language_code;
        }
        return $this->Request('getMyDescription', $data);
    }

    /**
     * Changes the bot's short description.
     *
     * @param string|null $short_description 0-120 characters
     * @param string|null $language_code
     * @return mixed
     */
    public function SetMyShortDescription($short_description = null, $language_code = null)
    {
        $data = [];
        if ($short_description !== null) {
            $data['short_description'] = $short_description;
        }
        if ($language_code !== null) {
            $data['language_code'] = $language_code;
        }
        return $this->Request('setMyShortDescription', $data);
    }

    /**
     * Gets the current bot short description.
     *
     * @param string|null $language_code
     * @return mixed
     */
    public function GetMyShortDescription($language_code = null)
    {
        $data = [];
        if ($language_code !== null) {
            $data['language_code'] = $language_code;
        }
        return $this->Request('getMyShortDescription', $data);
    }

    /**
     * Gets a list of profile pictures for a user.
     *
     * @param int $user_id
     * @param int|null $offset
     * @param int|null $limit
     * @return mixed
     */
    public function GetUserProfilePhotos($user_id, $offset = null, $limit = null)
    {
        $data = [
            'user_id' => $user_id,
            'offset' => $offset,
            'limit' => $limit,
        ];
        return $this->Request('getUserProfilePhotos', $data);
    }

    // =========================================================================
    // INLINE QUERIES & WEB APPS
    // =========================================================================

    /**
     * Answers a callback query sent from an inline keyboard.
     *
     * @param mixed $callbackid Put CallBack Query ID.
     * @param string|null $text Notification text (Optional).
     * @param bool $alert If true, an alert will be shown to the user instead of a banner.
     * @param array $extra Additional parameters (url, cache_time)
     * @return mixed
     */
    public function AnswerCallback($callbackid, $text = null, $alert = false, array $extra = [])
    {
        $data = [
            'callback_query_id' => $callbackid,
            'text' => $text,
            'show_alert' => (bool)$alert,
        ];
        return $this->Request('answerCallbackQuery', array_merge($data, $extra));
    }

    /**
     * Answers an inline query.
     *
     * @param mixed $id Put inlineQuery ID.
     * @param mixed $json Results as array or json string.
     * @param array $extra Additional parameters (cache_time, is_personal, next_offset, button)
     * @return mixed
     */
    public function AnswerInline($id, $json, array $extra = [])
    {
        $data = [
            'inline_query_id' => $id,
            'results' => is_string($json) ? $json : json_encode($json),
        ];
        return $this->Request('answerInlineQuery', array_merge($data, $extra));
    }

    /**
     * Sets the result of an interaction with a Web App and transforms a message on behalf of the user.
     *
     * @param string $web_app_query_id
     * @param array $result InlineQueryResult object
     * @return mixed
     */
    public function AnswerWebAppQuery($web_app_query_id, array $result)
    {
        return $this->Request('answerWebAppQuery', [
            'web_app_query_id' => $web_app_query_id,
            'result' => $result,
        ]);
    }

    // =========================================================================
    // KEYBOARD HELPERS & BUILDERS
    // =========================================================================

    /**
     * Builds a single button inline keyboard JSON string.
     *
     * @param string $text Button text.
     * @param string $url Target URL.
     * @return string
     */
    public function InlineKeyboard($text, $url)
    {
        return json_encode([
            'inline_keyboard' => [[['text' => $text, 'url' => $url]]],
            'resize_keyboard' => true
        ]);
    }

    /**
     * Encodes multiple buttons for inline keyboard.
     *
     * @param array|string $json Buttons and links as array or JSON.
     * @return string
     */
    public function MultiInlineKeyboard($json)
    {
        $buttons = is_string($json) ? json_decode($json, true) : $json;
        return json_encode([
            'inline_keyboard' => $buttons,
            'resize_keyboard' => true
        ]);
    }

    /**
     * Creates a single reply keyboard button.
     *
     * @param string $text Button text.
     * @return string
     */
    public function Keyboard($text)
    {
        return json_encode([
            'keyboard' => [[['text' => $text]]],
            'resize_keyboard' => true
        ]);
    }

    /**
     * Encodes a multi-row reply keyboard.
     *
     * @param array|string $json Array or JSON.
     * @return string
     */
    public function MultiKeyboard($json)
    {
        $buttons = is_string($json) ? json_decode($json, true) : $json;
        return json_encode([
            'keyboard' => $buttons,
            'resize_keyboard' => true
        ]);
    }

    /**
     * Removes current custom reply keyboard.
     *
     * @param bool $selective
     * @return string
     */
    public function RemoveKeyboard($selective = false)
    {
        return json_encode([
            'remove_keyboard' => true,
            'selective' => (bool)$selective
        ]);
    }

    /**
     * Alias for RemoveKeyboard().
     *
     * @param bool $selective
     * @return string
     */
    public function ReplyKeyboardRemove($selective = false)
    {
        return $this->RemoveKeyboard($selective);
    }

    /**
     * Constructs a modern InlineKeyboardMarkup array or JSON string.
     *
     * @param array $rows 2D array of inline button objects
     * @param bool $asJson If true returns JSON string, otherwise array
     * @return string|array
     */
    public function BuildInlineKeyboard(array $rows, $asJson = true)
    {
        $markup = ['inline_keyboard' => $rows];
        return $asJson ? json_encode($markup) : $markup;
    }

    /**
     * Helper to construct an InlineKeyboardButton.
     *
     * @param string $text Button display text
     * @param array $options Options (url, callback_data, web_app, switch_inline_query, switch_inline_query_current_chat, copy_text)
     * @return array
     */
    public function InlineButton($text, array $options = [])
    {
        return array_merge(['text' => $text], $options);
    }

    /**
     * Constructs a modern ReplyKeyboardMarkup array or JSON string.
     *
     * @param array $rows 2D array of keyboard button objects or strings
     * @param array $options (resize_keyboard, one_time_keyboard, is_persistent, input_field_placeholder, selective)
     * @param bool $asJson If true returns JSON string, otherwise array
     * @return string|array
     */
    public function BuildReplyKeyboard(array $rows, array $options = [], $asJson = true)
    {
        // Normalize rows: if a row contains strings, convert each to ['text' => string]
        $formattedRows = [];
        foreach ($rows as $row) {
            $formattedRow = [];
            foreach ($row as $btn) {
                if (is_string($btn)) {
                    $formattedRow[] = ['text' => $btn];
                } else {
                    $formattedRow[] = $btn;
                }
            }
            $formattedRows[] = $formattedRow;
        }

        $defaults = [
            'resize_keyboard' => true,
            'one_time_keyboard' => false,
        ];
        $markup = array_merge($defaults, $options, ['keyboard' => $formattedRows]);

        return $asJson ? json_encode($markup) : $markup;
    }

    /**
     * Helper to construct a KeyboardButton.
     *
     * @param string $text Button display text
     * @param array $options (request_contact, request_location, request_user, request_chat, request_poll, web_app)
     * @return array
     */
    public function ReplyButton($text, array $options = [])
    {
        return array_merge(['text' => $text], $options);
    }

    /**
     * Returns ForceReply markup.
     *
     * @param bool $selective
     * @param string|null $placeholder
     * @param bool $asJson
     * @return string|array
     */
    public function ForceReply($selective = false, $placeholder = null, $asJson = true)
    {
        $markup = [
            'force_reply' => true,
            'selective' => (bool)$selective,
        ];
        if ($placeholder !== null) {
            $markup['input_field_placeholder'] = $placeholder;
        }
        return $asJson ? json_encode($markup) : $markup;
    }

    // =========================================================================
    // UPDATE DATA EXTRACTION & GETTERS
    // =========================================================================

    /**
     * Returns the update ID of the current update.
     *
     * @return int|null
     */
    public function GetUpdateId()
    {
        return $this->Data['update_id'] ?? null;
    }

    /**
     * Resolves the current chat payload from various update structures.
     *
     * @return array|null
     */
    protected function ResolveChatData()
    {
        if (isset($this->Data['message']['chat'])) {
            return $this->Data['message']['chat'];
        }
        if (isset($this->Data['edited_message']['chat'])) {
            return $this->Data['edited_message']['chat'];
        }
        if (isset($this->Data['channel_post']['chat'])) {
            return $this->Data['channel_post']['chat'];
        }
        if (isset($this->Data['edited_channel_post']['chat'])) {
            return $this->Data['edited_channel_post']['chat'];
        }
        if (isset($this->Data['callback_query']['message']['chat'])) {
            return $this->Data['callback_query']['message']['chat'];
        }
        if (isset($this->Data['my_chat_member']['chat'])) {
            return $this->Data['my_chat_member']['chat'];
        }
        if (isset($this->Data['chat_member']['chat'])) {
            return $this->Data['chat_member']['chat'];
        }
        if (isset($this->Data['chat_join_request']['chat'])) {
            return $this->Data['chat_join_request']['chat'];
        }
        if (isset($this->Data['chat_boost']['chat'])) {
            return $this->Data['chat_boost']['chat'];
        }
        if (isset($this->Data['removed_chat_boost']['chat'])) {
            return $this->Data['removed_chat_boost']['chat'];
        }
        if (isset($this->Data['message_reaction']['chat'])) {
            return $this->Data['message_reaction']['chat'];
        }
        if (isset($this->Data['message_reaction_count']['chat'])) {
            return $this->Data['message_reaction_count']['chat'];
        }
        if (isset($this->Data['business_message']['chat'])) {
            return $this->Data['business_message']['chat'];
        }
        return null;
    }

    /**
     * Resolves the sender user payload ('from') from various update structures.
     *
     * @return array|null
     */
    protected function ResolveFromData()
    {
        if (isset($this->Data['message']['from'])) {
            return $this->Data['message']['from'];
        }
        if (isset($this->Data['edited_message']['from'])) {
            return $this->Data['edited_message']['from'];
        }
        if (isset($this->Data['callback_query']['from'])) {
            return $this->Data['callback_query']['from'];
        }
        if (isset($this->Data['inline_query']['from'])) {
            return $this->Data['inline_query']['from'];
        }
        if (isset($this->Data['chosen_inline_result']['from'])) {
            return $this->Data['chosen_inline_result']['from'];
        }
        if (isset($this->Data['shipping_query']['from'])) {
            return $this->Data['shipping_query']['from'];
        }
        if (isset($this->Data['pre_checkout_query']['from'])) {
            return $this->Data['pre_checkout_query']['from'];
        }
        if (isset($this->Data['my_chat_member']['from'])) {
            return $this->Data['my_chat_member']['from'];
        }
        if (isset($this->Data['chat_member']['from'])) {
            return $this->Data['chat_member']['from'];
        }
        if (isset($this->Data['chat_join_request']['from'])) {
            return $this->Data['chat_join_request']['from'];
        }
        if (isset($this->Data['message_reaction']['user'])) {
            return $this->Data['message_reaction']['user'];
        }
        if (isset($this->Data['business_connection']['user'])) {
            return $this->Data['business_connection']['user'];
        }
        if (isset($this->Data['business_message']['from'])) {
            return $this->Data['business_message']['from'];
        }
        return null;
    }

    /**
     * Resolves active message payload from update.
     *
     * @return array|null
     */
    protected function ResolveMessageData()
    {
        if (isset($this->Data['message'])) {
            return $this->Data['message'];
        }
        if (isset($this->Data['edited_message'])) {
            return $this->Data['edited_message'];
        }
        if (isset($this->Data['channel_post'])) {
            return $this->Data['channel_post'];
        }
        if (isset($this->Data['edited_channel_post'])) {
            return $this->Data['edited_channel_post'];
        }
        if (isset($this->Data['business_message'])) {
            return $this->Data['business_message'];
        }
        if (isset($this->Data['callback_query']['message'])) {
            return $this->Data['callback_query']['message'];
        }
        return null;
    }

    /**
     * Returns Current ChatID across any message or callback.
     *
     * @return int|string|null
     */
    public function GetChatID()
    {
        $chat = $this->ResolveChatData();
        return $chat['id'] ?? null;
    }

    /**
     * Returns Sender UserID across any message or interaction.
     *
     * @return int|string|null
     */
    public function FromID()
    {
        $from = $this->ResolveFromData();
        return $from['id'] ?? null;
    }

    /**
     * Returns ID of the message.
     *
     * @return int|null
     */
    public function MessageID()
    {
        $msg = $this->ResolveMessageData();
        return $msg['message_id'] ?? null;
    }

    /**
     * Returns the thread ID / topic ID for forum supergroups.
     *
     * @return int|null
     */
    public function GetThreadID()
    {
        $msg = $this->ResolveMessageData();
        return $msg['message_thread_id'] ?? null;
    }

    /**
     * Returns Message Text.
     *
     * @return string|null
     */
    public function GetText()
    {
        $msg = $this->ResolveMessageData();
        return $msg['text'] ?? null;
    }

    /**
     * Returns Caption of Media Message.
     *
     * @return string|null
     */
    public function GetCaption()
    {
        $msg = $this->ResolveMessageData();
        return $msg['caption'] ?? null;
    }

    /**
     * Returns Current Sender's Username.
     *
     * @return string|null
     */
    public function GetUsername()
    {
        $from = $this->ResolveFromData();
        return $from['username'] ?? null;
    }

    /**
     * Returns Current Sender's First Name.
     *
     * @return string|null
     */
    public function GetFirstname()
    {
        $from = $this->ResolveFromData();
        return $from['first_name'] ?? null;
    }

    /**
     * Returns Current Sender's Last Name.
     *
     * @return string|null
     */
    public function GetLastname()
    {
        $from = $this->ResolveFromData();
        return $from['last_name'] ?? null;
    }

    /**
     * Returns sender's combined full name.
     *
     * @return string
     */
    public function GetFullName()
    {
        $first = $this->GetFirstname();
        $last = $this->GetLastname();
        return trim($first . ' ' . $last);
    }

    /**
     * Checks if current sender has Telegram Premium.
     *
     * @return bool
     */
    public function IsPremium()
    {
        $from = $this->ResolveFromData();
        return !empty($from['is_premium']);
    }

    /**
     * Checks if current sender is a bot.
     *
     * @return bool
     */
    public function IsBot()
    {
        $from = $this->ResolveFromData();
        return !empty($from['is_bot']);
    }

    /**
     * Returns current user's IETF language code.
     *
     * @return string|null
     */
    public function GetLanguageCode()
    {
        $from = $this->ResolveFromData();
        return $from['language_code'] ?? null;
    }

    /**
     * Returns Current Chat Username.
     *
     * @return string|null
     */
    public function GetChatUser()
    {
        $chat = $this->ResolveChatData();
        return $chat['username'] ?? null;
    }

    /**
     * Returns Chat Type ('private', 'group', 'supergroup', 'channel').
     *
     * @return string|null
     */
    public function ChatType()
    {
        $chat = $this->ResolveChatData();
        return $chat['type'] ?? null;
    }

    /**
     * Returns Current Chat Title.
     *
     * @return string|null
     */
    public function ChatTitle()
    {
        $chat = $this->ResolveChatData();
        return $chat['title'] ?? null;
    }

    /**
     * Returns message timestamp (Unix).
     *
     * @return int|null
     */
    public function GetDate()
    {
        $msg = $this->ResolveMessageData();
        return $msg['date'] ?? null;
    }

    /**
     * Returns Identifier Of Forwarded Message Origin At Reply.
     *
     * @return int|string|null
     */
    public function ForwarderID()
    {
        if (isset($this->Data['message']['reply_to_message']['forward_from']['id'])) {
            return $this->Data['message']['reply_to_message']['forward_from']['id'];
        }
        if (isset($this->Data['message']['forward_from']['id'])) {
            return $this->Data['message']['forward_from']['id'];
        }
        return null;
    }

    /**
     * Returns reply_to_message payload if current message is a reply.
     *
     * @return array|null
     */
    public function GetReplyToMessage()
    {
        return $this->Data['message']['reply_to_message'] ?? null;
    }

    /**
     * Returns quote payload if current message quotes another message.
     *
     * @return array|null
     */
    public function GetQuote()
    {
        return $this->Data['message']['quote'] ?? null;
    }

    /**
     * Returns business connection ID if message was sent via business bot.
     *
     * @return string|null
     */
    public function GetBusinessConnectionId()
    {
        if (isset($this->Data['business_message']['business_connection_id'])) {
            return $this->Data['business_message']['business_connection_id'];
        }
        if (isset($this->Data['business_connection']['id'])) {
            return $this->Data['business_connection']['id'];
        }
        return null;
    }

    // =========================================================================
    // BOT COMMAND HELPERS
    // =========================================================================

    /**
     * Checks if current message text starts with a bot command slash ('/').
     *
     * @return bool
     */
    public function IsCommand()
    {
        $text = $this->GetText();
        return !empty($text) && substr($text, 0, 1) === '/';
    }

    /**
     * Returns the command name without the leading slash and without '@botname'.
     * Example: for "/start@my_bot 123", returns "start".
     *
     * @return string|null
     */
    public function GetCommand()
    {
        $text = $this->GetText();
        if (empty($text) || substr($text, 0, 1) !== '/') {
            return null;
        }

        $parts = explode(' ', substr($text, 1), 2);
        $command = $parts[0];
        if (strpos($command, '@') !== false) {
            $command = explode('@', $command)[0];
        }

        return strtolower($command);
    }

    /**
     * Returns command arguments as an array.
     * Example: for "/kick 12345 spam", returns ['12345', 'spam'].
     *
     * @return array
     */
    public function GetCommandArgs()
    {
        $text = $this->GetText();
        if (empty($text) || substr($text, 0, 1) !== '/') {
            return [];
        }

        $parts = preg_split('/\s+/', trim($text));
        array_shift($parts); // remove the command itself
        return $parts;
    }

    /**
     * Returns command arguments as a single string.
     * Example: for "/echo Hello World", returns "Hello World".
     *
     * @return string
     */
    public function GetCommandArgsString()
    {
        $text = $this->GetText();
        if (empty($text) || substr($text, 0, 1) !== '/') {
            return '';
        }

        $parts = explode(' ', $text, 2);
        return isset($parts[1]) ? trim($parts[1]) : '';
    }

    // =========================================================================
    // CONTENT TYPE & MEDIA INSPECTORS
    // =========================================================================

    /**
     * Return Message Content Type.
     *
     * @return string|null
     */
    public function MessageType()
    {
        $msg = $this->ResolveMessageData();
        if (!$msg) {
            return null;
        }

        if (isset($msg['text'])) {
            return self::TEXT;
        }
        if (isset($msg['photo'])) {
            return self::PHOTO;
        }
        if (isset($msg['video'])) {
            return self::VIDEO;
        }
        if (isset($msg['audio'])) {
            return self::AUDIO;
        }
        if (isset($msg['voice'])) {
            return self::VOICE;
        }
        if (isset($msg['animation'])) {
            return self::ANIMATION;
        }
        if (isset($msg['document'])) {
            return self::DOCUMENT;
        }
        if (isset($msg['sticker'])) {
            return self::STICKER;
        }
        if (isset($msg['video_note'])) {
            return self::VIDEO_NOTE;
        }
        if (isset($msg['contact'])) {
            return self::CONTACT;
        }
        if (isset($msg['location'])) {
            return self::LOCATION;
        }
        if (isset($msg['venue'])) {
            return self::VENUE;
        }
        if (isset($msg['poll'])) {
            return self::POLL;
        }
        if (isset($msg['dice'])) {
            return self::DICE;
        }
        if (isset($msg['game'])) {
            return self::GAME;
        }
        if (isset($msg['invoice'])) {
            return self::INVOICE;
        }
        if (isset($msg['successful_payment'])) {
            return self::SUCCESSFUL_PAYMENT;
        }
        if (isset($msg['paid_media'])) {
            return self::PAID_MEDIA;
        }
        if (isset($msg['story'])) {
            return self::STORY;
        }

        return null;
    }

    /**
     * Returns top-level update category / type.
     *
     * @return string|null
     */
    public function GetUpdateType()
    {
        if (isset($this->Data['message'])) {
            return 'message';
        }
        if (isset($this->Data['edited_message'])) {
            return self::EDITED_MESSAGE;
        }
        if (isset($this->Data['channel_post'])) {
            return self::CHANNEL_POST;
        }
        if (isset($this->Data['edited_channel_post'])) {
            return self::EDITED_CHANNEL_POST;
        }
        if (isset($this->Data['business_connection'])) {
            return self::BUSINESS_CONNECTION;
        }
        if (isset($this->Data['business_message'])) {
            return self::BUSINESS_MESSAGE;
        }
        if (isset($this->Data['edited_business_message'])) {
            return self::EDITED_BUSINESS_MESSAGE;
        }
        if (isset($this->Data['deleted_business_messages'])) {
            return self::DELETED_BUSINESS_MESSAGES;
        }
        if (isset($this->Data['message_reaction'])) {
            return self::MESSAGE_REACTION;
        }
        if (isset($this->Data['message_reaction_count'])) {
            return self::MESSAGE_REACTION_COUNT;
        }
        if (isset($this->Data['inline_query'])) {
            return self::INLINE_QUERY;
        }
        if (isset($this->Data['chosen_inline_result'])) {
            return self::CHOSEN_INLINE_RESULT;
        }
        if (isset($this->Data['callback_query'])) {
            return self::CALLBACK_QUERY;
        }
        if (isset($this->Data['shipping_query'])) {
            return self::SHIPPING_QUERY;
        }
        if (isset($this->Data['pre_checkout_query'])) {
            return self::PRE_CHECKOUT_QUERY;
        }
        if (isset($this->Data['poll'])) {
            return self::POLL_UPDATE;
        }
        if (isset($this->Data['poll_answer'])) {
            return self::POLL_ANSWER;
        }
        if (isset($this->Data['my_chat_member'])) {
            return self::MY_CHAT_MEMBER;
        }
        if (isset($this->Data['chat_member'])) {
            return self::CHAT_MEMBER;
        }
        if (isset($this->Data['chat_join_request'])) {
            return self::CHAT_JOIN_REQUEST;
        }
        if (isset($this->Data['chat_boost'])) {
            return self::CHAT_BOOST;
        }
        if (isset($this->Data['removed_chat_boost'])) {
            return self::REMOVED_CHAT_BOOST;
        }
        if (isset($this->Data['purchased_paid_media'])) {
            return self::PURCHASED_PAID_MEDIA;
        }

        return null;
    }

    /**
     * Returns Current FileID of the received media.
     *
     * @param string $type photo, video, audio, voice, document, animation, sticker, video_note
     * @return string|null
     */
    public function GetFileID($type)
    {
        $msg = $this->ResolveMessageData();
        if (!$msg) {
            return null;
        }

        switch ($type) {
            case 'photo':
                if (isset($msg['photo']) && is_array($msg['photo'])) {
                    // Highest resolution photo is last in the array
                    $last = end($msg['photo']);
                    return $last['file_id'] ?? null;
                }
                break;
            case 'video':
                return $msg['video']['file_id'] ?? null;
            case 'audio':
                return $msg['audio']['file_id'] ?? null;
            case 'voice':
                return $msg['voice']['file_id'] ?? null;
            case 'document':
                return $msg['document']['file_id'] ?? null;
            case 'animation':
                return $msg['animation']['file_id'] ?? null;
            case 'sticker':
                return $msg['sticker']['file_id'] ?? null;
            case 'video_note':
                return $msg['video_note']['file_id'] ?? null;
        }

        return null;
    }

    /**
     * Gets file information and prepares full download URL.
     *
     * @param string $param Fill With (path, id, unique, size, url).
     * @return string|int|null
     */
    public function FileOptions($param)
    {
        $fileId = $this->Array['file_id'] ?? null;
        if (empty($fileId)) {
            return null;
        }

        $res = $this->GetFile($fileId);
        $result = is_object($res) ? (array)($res->result ?? []) : ($res['result'] ?? []);

        switch ($param) {
            case 'path':
                return $result['file_path'] ?? null;
            case 'id':
                return $result['file_id'] ?? null;
            case 'unique':
                return $result['file_unique_id'] ?? null;
            case 'size':
                return $result['file_size'] ?? null;
            case 'url':
            default:
                if (!empty($result['file_path'])) {
                    $token = $this->Token ?: (defined('API_KEY') ? API_KEY : '');
                    return rtrim($this->ApiUrl, '/') . '/file/bot' . $token . '/' . $result['file_path'];
                }
                return null;
        }
    }

    /**
     * Calls getFile endpoint to prepare file for download.
     *
     * @param string $file_id Your FileID Stored On Telegram Servers.
     * @return mixed
     */
    public function GetFile($file_id)
    {
        $this->Array = ['file_id' => $file_id];
        return $this->Request('getFile', ['file_id' => $file_id]);
    }

    /**
     * Generates directly downloadable URL for a Telegram file.
     *
     * @param string $file_id
     * @return string|null
     */
    public function GetFileUrl($file_id)
    {
        $res = $this->GetFile($file_id);
        $filePath = null;
        if (is_object($res) && isset($res->result->file_path)) {
            $filePath = $res->result->file_path;
        } elseif (is_array($res) && isset($res['result']['file_path'])) {
            $filePath = $res['result']['file_path'];
        }

        if ($filePath) {
            $token = $this->Token ?: (defined('API_KEY') ? API_KEY : '');
            return rtrim($this->ApiUrl, '/') . '/file/bot' . $token . '/' . $filePath;
        }

        return null;
    }

    /**
     * Returns Audio / Music metadata.
     *
     * @param string $value Fill With (title, artist, performer, mime, thumb, thumbnail, size, duration, id).
     * @return mixed
     */
    public function Music($value)
    {
        $msg = $this->ResolveMessageData();
        $audio = $msg['audio'] ?? [];

        switch ($value) {
            case 'title':
                return $audio['title'] ?? null;
            case 'artist':
            case 'performer':
                return $audio['performer'] ?? null;
            case 'mime':
                return $audio['mime_type'] ?? null;
            case 'thumb':
            case 'thumbnail':
                return $audio['thumbnail'] ?? ($audio['thumb'] ?? null);
            case 'size':
                return $audio['file_size'] ?? null;
            case 'duration':
                return $audio['duration'] ?? null;
            case 'id':
                return $audio['file_id'] ?? null;
        }

        return null;
    }

    /**
     * Returns Document metadata.
     *
     * @param string $value Fill With (name, id, size, unique, thumb, thumbnail, mime).
     * @return mixed
     */
    public function Document($value)
    {
        $msg = $this->ResolveMessageData();
        $doc = $msg['document'] ?? [];

        switch ($value) {
            case 'name':
                return $doc['file_name'] ?? null;
            case 'id':
                return $doc['file_id'] ?? null;
            case 'mime':
                return $doc['mime_type'] ?? null;
            case 'thumb':
            case 'thumbnail':
                return $doc['thumbnail'] ?? ($doc['thumb'] ?? null);
            case 'size':
                return $doc['file_size'] ?? null;
            case 'unique':
                return $doc['file_unique_id'] ?? null;
        }

        return null;
    }

    /**
     * Returns Contact metadata.
     *
     * @param string $value Fill With (phone, first, last, id, vcard).
     * @return mixed
     */
    public function Contact($value)
    {
        $msg = $this->ResolveMessageData();
        $contact = $msg['contact'] ?? [];

        switch ($value) {
            case 'phone':
                return $contact['phone_number'] ?? null;
            case 'first':
                return $contact['first_name'] ?? null;
            case 'last':
                return $contact['last_name'] ?? null;
            case 'id':
                return $contact['user_id'] ?? null;
            case 'vcard':
                return $contact['vcard'] ?? null;
        }

        return null;
    }

    /**
     * Returns CallBackQuery details.
     *
     * @param string $select Fill With (id, from, data, chatid, msgid).
     * @return mixed
     */
    public function CallBackQuery($select)
    {
        $cb = $this->Data['callback_query'] ?? [];

        switch ($select) {
            case 'id':
                return $cb['id'] ?? null;
            case 'from':
                return $cb['from']['id'] ?? null;
            case 'data':
                return $cb['data'] ?? null;
            case 'chatid':
                return $cb['message']['chat']['id'] ?? null;
            case 'msgid':
                return $cb['message']['message_id'] ?? null;
        }

        return null;
    }

    /**
     * Returns InlineQuery details.
     *
     * @param string $select Fill With (id, query, offset, chat_type).
     * @return mixed
     */
    public function InlineQuery($select)
    {
        $iq = $this->Data['inline_query'] ?? [];

        switch ($select) {
            case 'id':
                return $iq['id'] ?? null;
            case 'query':
                return $iq['query'] ?? null;
            case 'offset':
                return $iq['offset'] ?? null;
            case 'chat_type':
                return $iq['chat_type'] ?? null;
        }

        return null;
    }
}
