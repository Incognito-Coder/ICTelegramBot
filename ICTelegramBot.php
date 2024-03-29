<?php

namespace TelegramBot;

error_reporting(0);
/**
 * @author Incognito Coder
 * @copyright 2020-2023 ICDev
 * @version 1.5.4
 */
class ICBot
{
    const TEXT = 'text';
    const PHOTO = 'photo';
    const VIDEO = 'video';
    const DOCUMENT = 'document';
    const AUDIO = 'music';
    const VOICE = 'voice';
    const CONTACT = 'contact';
    const CALLBACK_QUERY = 'callback_query';
    const INLINE_QUERY = 'inline_query';
    private $Data = [];
    private $Array = [];

    public function __construct()
    {
        $this->Data = $this->Update();
    }

    public function Update()
    {
        if (empty($this->Data)) {
            $update = file_get_contents('php://input');
            return json_decode($update, true);
        } else {
            return $this->Data;
        }
    }

    /**
     * @param string $Token Your Bot API-KEY, Get It From @BotFather
     */
    public function Initialize($Token)
    {
        define('API_KEY', $Token);
        function BOT($Method, $Data = [])
        {
            $url = "https://api.telegram.org/bot" . API_KEY . "/" . $Method;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $Data);
            $res = curl_exec($ch);
            if (curl_error($ch)) {
                var_dump(curl_error($ch));
            } else {
                return json_decode($res);
            }
        }
    }

    /**
     * @meta This Function Easily Sets WebHook.
     * @param $Input Enter Url.
     */
    function SetWebHook($Input)
    {
        BOT('setWebhook', ['url' => $Input]);
    }

    /**
     * @param mixed $chat Target ChatID.
     * @param string $text Body of Message To Send.
     * @param string $parse Must be HTML or MarkDown (Optional)
     * @param boolean $preview Disables link previews for links in this message (Optional)
     * @param boolean $notification Sends the message silently. Users will receive a notification with no sound (Optional)
     * @param integer $reply If the message is a reply, ID of the original message (Optional)
     * @param mixed $keyboard Put Variable or Leave It Null (Optional)
     */
    function SendMessage($chat, $text, $parse = null, $preview = null, $notification = null, $reply = null, $keyboard = null)
    {
        BOT('sendMessage', [
            'chat_id' => $chat,
            'text' => $text,
            'parse_mode' => $parse,
            'disable_web_page_preview' => $preview,
            'disable_notification' => $notification,
            'reply_to_message_id' => $reply,
            'reply_markup' => $keyboard,
        ]);
    }

    /**
     * @param mixed $chat Target ChatID.
     * @param int $msgid ID of Message.
     * @param string $text Body of Message To Send.
     * @param boolean $parse Must be HTML or MarkDown (Optional)
     * @param null $keyboard Put Variable or Leave It Null (Optional)
     */
    function EditMessage($chat, $msgid, $text, $parse, $keyboard = null)
    {
        BOT('editMessageText', [
            'chat_id' => $chat,
            'message_id' => $msgid,
            'text' => $text,
            'parse_mode' => $parse,
            'reply_markup' => $keyboard
        ]);
    }

    /**
     * @param mixed $chat Target ChatID.
     * @param int $msgid ID of Message.
     * @param string $caption New caption of the message, 0-1024 characters after entities parsing.
     * @param boolean $parse Must be HTML or MarkDown (Optional)
     * @param null $keyboard Put Variable or Leave It Null (Optional)
     */
    function EditMessageCaption($chat, $msgid, $caption, $parse, $keyboard = null)
    {
        BOT('editMessageCaption', [
            'chat_id' => $chat,
            'message_id' => $msgid,
            'caption' => $caption,
            'parse_mode' => $parse,
            'reply_markup' => $keyboard
        ]);
    }

    /**
     * @param mixed $chat Target ChatID.
     * @param int $msgid ID of Message.
     */
    function DeleteMessage($chat, $msgid)
    {
        BOT('deleteMessage', [
            'chat_id' => $chat,
            'message_id' => $msgid
        ]);
    }

    /**
     * @param mixed $chat Target ChatID.
     * @param string $file Photo To Send.
     * @param string $caption Some Details About Image (Optional)
     * @param string $parse Must be HTML or MarkDown (Optional)
     * @param boolean $notification Sends the message silently. Users will receive a notification with no sound (Optional)
     * @param integer $reply If the message is a reply, ID of the original message (Optional)
     * @param mixed $keyboard Put Variable or Leave It Null (Optional)
     */
    function SendPhoto($chat, $file, $caption = null, $parse = null, $notification = null, $reply = null, $keyboard = null)
    {
        BOT('sendPhoto', [
            'chat_id' => $chat,
            'photo' => $file,
            'caption' => $caption,
            'parse_mode' => $parse,
            'disable_notification' => $notification,
            'reply_to_message_id' => $reply,
            'reply_markup' => $keyboard
        ]);
    }

    /**
     * @param mixed $chat Target ChatID.
     * @param string $file Video To Send.
     * @param string $caption Some Details About Video (Optional)
     * @param string $parse Must be HTML or MarkDown (Optional)
     * @param boolean $notification Sends the message silently. Users will receive a notification with no sound (Optional)
     * @param integer $reply If the message is a reply, ID of the original message (Optional)
     * @param mixed $keyboard Put Variable or Leave It Null (Optional)
     */
    function SendVideo($chat, $file, $caption = null, $parse = null, $notification = null, $reply = null, $keyboard = null)
    {
        BOT('sendVideo', [
            'chat_id' => $chat,
            'video' => $file,
            'caption' => $caption,
            'parse_mode' => $parse,
            'disable_notification' => $notification,
            'reply_to_message_id' => $reply,
            'reply_markup' => $keyboard
        ]);
    }

    /**
     * @param mixed $chat Target ChatID.
     * @param string $file Music To Send.
     * @param string $caption Some Details About Audio (Optional)
     * @param string $parse Must be HTML or MarkDown (Optional)
     * @param integer $duration Duration of the audio in seconds (Optional)
     * @param string $performer Performer (Optional)
     * @param string $title Track name (Optional)
     * @param string $thumb Thumbnail of the file sent (Optional)
     * @param boolean $notification Sends the message silently. Users will receive a notification with no sound (Optional)
     * @param integer $reply If the message is a reply, ID of the original message (Optional)
     * @param mixed $keyboard Put Variable or Leave It Null (Optional)
     */
    function SendAudio($chat, $file, $caption = null, $parse = null, $duration = null, $performer = null, $title = null, $thumb = null, $notification = null, $reply = null, $keyboard = null)
    {
        BOT('sendAudio', [
            'chat_id' => $chat,
            'audio' => $file,
            'caption' => $caption,
            'parse_mode' => $parse,
            'duration' => $duration,
            'title' => $title,
            'thumb' => $thumb,
            'performer' => $performer,
            'disable_notification' => $notification,
            'reply_to_message_id' => $reply,
            'reply_markup' => $keyboard
        ]);
    }

    /**
     * @param mixed $chat Target ChatID.
     * @param string $file Audio To Send.
     * @param string $caption Some Details About Voice (Optional)
     * @param string $parse Must be HTML or MarkDown (Optional)
     * @param integer $duration Duration of the voice message in seconds (Optional)
     * @param boolean $notification Sends the message silently. Users will receive a notification with no sound (Optional)
     * @param integer $reply If the message is a reply, ID of the original message (Optional)
     * @param mixed $keyboard Put Variable or Leave It Null (Optional)
     */
    function SendVoice($chat, $file, $caption = null, $parse = null, $duration = null, $notification = null, $reply = null, $keyboard = null)
    {
        BOT('sendVoice', [
            'chat_id' => $chat,
            'voice' => $file,
            'caption' => $caption,
            'parse_mode' => $parse,
            'duration' => $duration,
            'disable_notification' => $notification,
            'reply_to_message_id' => $reply,
            'reply_markup' => $keyboard
        ]);
    }

    /**
     * @param mixed $chat Target ChatID.
     * @param string $file Document To Send.
     * @param string $caption Some Details About File (Optional)
     * @param string $parse Must be HTML or MarkDown (Optional)
     * @param string $thumb Mini Image For File(Optional)
     * @param boolean $notification Sends the message silently. Users will receive a notification with no sound (Optional)
     * @param integer $reply If the message is a reply, ID of the original message (Optional)
     * @param mixed $keyboard Put Variable or Leave It Null (Optional)
     */
    function SendDocument($chat, $file, $caption = null, $parse = null, $thumb = null, $notification = null, $reply = null, $keyboard = null)
    {
        BOT('sendDocument', [
            'chat_id' => $chat,
            'document' => $file,
            'thumb' => $thumb,
            'caption' => $caption,
            'parse_mode' => $parse,
            'disable_notification' => $notification,
            'reply_to_message_id' => $reply,
            'reply_markup' => $keyboard
        ]);
    }

    /**
     * @param mixed $chat Target ChatID.
     * @param mixed $action Type of action to broadcast. Choose one, depending on what the user is about to receive: typing for text messages, upload_photo for photos, record_video or upload_video for videos, record_voice or upload_voice for voice notes, upload_document for general files, find_location for location data, record_video_note or upload_video_note for video notes.
     */
    function SendChatAction($chat, $action)
    {
        BOT('sendChatAction', [
            'chat_id' => $chat,
            'action' => $action
        ]);
    }

    /**
     * @param mixed $chat Target ChatID.
     * @param mixed $from Forward Message From ChatID.
     * @param mixed $msgid Desired Message ID.
     */
    function ForwardMessage($chat, $from, $msgid)
    {
        BOT('ForwardMessage', [
            'chat_id' => $chat,
            'from_chat_id' => $from,
            'message_id' => $msgid
        ]);
    }

    /**
     * @param string $text Enter Button Text.
     * @param string $url Link You Want To Be Open.
     */
    public function InlineKeyboard($text, $url)
    {
        return json_encode(['inline_keyboard' => [[['text' => $text, 'url' => $url]]], 'resize_keyboard' => true]);
    }

    /**
     * @param string $json Enter Buttons And Links As Json.
     */
    public function MultiInlineKeyboard($json)
    {
        return json_encode(['inline_keyboard' => $json, 'resize_keyboard' => true]);
    }

    /**
     * @param string $text Enter Button Text.
     */
    public function Keyboard($text)
    {
        return json_encode(['keyboard' => [[['text' => $text]]], 'resize_keyboard' => true]);
    }

    /**
     * @param string $json Enter Buttons As Json.
     */
    public function MultiKeyboard($json)
    {
        return json_encode(['keyboard' => $json, 'resize_keyboard' => true]);
    }

    /**
     * @meta Remove Keyboard.
     */
    public function RemoveKeyboard()
    {
        return json_encode(['remove_keyboard' => true]);
    }

    /**
     * @meta Return Current ChatID.
     */
    public function GetChatID()
    {
        return $this->Data['message']['chat']['id'];
    }

    /**
     * @meta Get Message of User.
     */
    public function GetText()
    {
        return $this->Data['message']['text'];
    }

    /**
     * @meta Get Caption of Message.
     */
    public function GetCaption()
    {
        return $this->Data['message']['caption'];
    }

    /**
     * @meta Get Current User Username.
     */
    public function GetUsername()
    {
        return $this->Data['message']['from']['username'];
    }

    /**
     * @meta Get Current User Firstname.
     */
    public function GetFirstname()
    {
        return $this->Data['message']['from']['first_name'];
    }

    /**
     * @meta Return ID of Last Received Message.
     */
    public function MessageID()
    {
        return $this->Data['message']['message_id'];
    }

    /**
     * @meta Return Current Chat Username.
     */
    public function GetChatUser()
    {
        return $this->Data['message']['chat']['username'];
    }

    /**
     * @meta Return Chat Type.
     */
    public function ChatType()
    {
        return $this->Data['message']['chat']['type'];
    }

    /**
     * @meta Return user ChatID.
     */
    public function FromID()
    {
        return $this->Data['message']['from']['id'];
    }

    /**
     * @meta Return Current Chat Name.
     */
    public function ChatTitle()
    {
        return $this->Data['message']['chat']['title'];
    }

    /**
     * @meta Return Identifier Of Forwarded Message At Reply.
     */
    public function ForwarderID()
    {
        return $this->Data['message']['reply_to_message']['forward_from']['id'];
    }

    /**
     * @meta Check Is User Joined On Desired Chat.
     * @param string $chatid Fill With (username,id).
     * @param string $userid Enter UserID.
     */
    function GetChatMember($chatid, $userid)
    {
        $check = json_decode(file_get_contents('https://api.telegram.org/bot' . API_KEY . "/getChatMember?chat_id=$chatid&user_id=" . $userid))->result->status;
        return $check;
    }

    /**
     * @meta Return Update Type.
     */
    public function MessageType()
    {
        if (isset($this->Data['message']['text'])) {
            return self::TEXT;
        }
        if (isset($this->Data['message']['photo'])) {
            return self::PHOTO;
        }
        if (isset($this->Data['message']['video'])) {
            return self::VIDEO;
        }
        if (isset($this->Data['message']['audio'])) {
            return self::AUDIO;
        }
        if (isset($this->Data['message']['voice'])) {
            return self::VOICE;
        }
        if (isset($this->Data['message']['document'])) {
            return self::DOCUMENT;
        }
        if (isset($this->Data['message']['contact'])) {
            return self::CONTACT;
        }
    }

    /**
     * @meta Return Current FileID.
     * @param string $type Fill With (photo,video,audio,voice,document).
     */
    public function GetFileID($type)
    {
        switch ($type) {
            case 'photo';
                $new = $this->Data['message']['photo'][0]['file_id'];
                break;
            case 'video';
                $new = $this->Data['message']['video']['file_id'];
                break;
            case 'audio';
                $new = $this->Data['message']['audio']['file_id'];
                break;
            case 'voice';
                $new = $this->Data['message']['voice']['file_id'];
                break;
            case 'document';
                $new = $this->Data['message']['document']['file_id'];
                break;
        }
        return $new;
    }

    /**
     * @meta Return Current FileID.
     * @param string $param Fill With (path,id,unique,size).
     */
    public function FileOptions($param)
    {
        $return = $this->GetFile($this->Array["file_id"]);
        switch ($param) {
            case 'path';
                $new = $return['result']['file_path'];
                break;
            case 'id';
                $new = $return['result']['file_id'];
                break;
            case 'unique';
                $new = $return['result']['file_unique_id'];
                break;
            case 'size';
                $new = $return['result']['file_size'];
                break;
        }
        $result = 'https://api.telegram.org/file/bot' . API_KEY . '/' . $new;
        return $result;
    }

    /**
     * @param string $file_id Your FileID Stored On Telegram Servers.
     */
    public function GetFile($file_id)
    {

        $this->Array = get_defined_vars();
        $download = json_decode(file_get_contents('https://api.telegram.org/bot' . API_KEY . '/getFile?file_id=' . $file_id), true);
        return $download;
    }

    /**
     * @meta Return Music Details.
     * @param string $value Fill With (title,artist,mime,thumb,size).
     */
    public function Music($value)
    {
        switch ($value) {
            case 'title';
                $new = $this->Data['message']['audio']['title'];
                break;
            case 'artist';
                $new = $this->Data['message']['audio']['performer'];
                break;
            case 'mime';
                $new = $this->Data['message']['audio']['mime_type'];
                break;
            case 'thumb';
                $new = $this->Data['message']['audio']['thumb'];
                break;
            case 'size';
                $new = $this->Data['message']['audio']['file_size'];
                break;
        }
        return $new;
    }


    /**
     * @meta Return Document Details.
     * @param string $value Fill With (name,id,size,unique,thumb,mime).
     */
    public function Document($value)
    {
        switch ($value) {
            case 'name';
                $new = $this->Data['message']['document']['file_name'];
                break;
            case 'id';
                $new = $this->Data['message']['document']['file_id'];
                break;
            case 'mime';
                $new = $this->Data['message']['document']['mime_type'];
                break;
            case 'thumb';
                $new = $this->Data['message']['document']['thumb'];
                break;
            case 'size';
                $new = $this->Data['message']['document']['file_size'];
                break;
            case 'unique';
                $new = $this->Data['message']['document']['file_unique_id'];
        }
        return $new;
    }

    /**
     * @meta Return Contact Details.
     * @param string $value Fill With (phone,first,last,id,vcard).
     */
    public function Contact($value)
    {
        switch ($value) {
            case 'phone';
                $new = $this->Data['message']['contact']['phone_number'];
                break;
            case 'first';
                $new = $this->Data['message']['contact']['first_name'];
                break;
            case 'last';
                $new = $this->Data['message']['contact']['last_name'];
                break;
            case 'id';
                $new = $this->Data['message']['contact']['user_id'];
                break;
            case 'vcard';
                $new = $this->Data['message']['contact']['vcard'];
                break;
        }
        return $new;
    }

    /**
     * @meta Return CallBack Details.
     * @param string $select Fill With (id,from,data,chatid,msgid).
     * @return mixed
     */
    public function CallBackQuery($select)
    {
        switch ($select) {
            case 'id';
                $return = $this->Data['callback_query']['id'];
                break;
            case 'from';
                $return = $this->Data['callback_query']['from']['id'];
                break;
            case 'data';
                $return = $this->Data['callback_query']['data'];
                break;
            case 'chatid';
                $return = $this->Data['callback_query']['message']['chat']['id'];
                break;
            case 'msgid';
                $return = $this->Data['callback_query']['message']['message_id'];
                break;
        }
        return $return;
    }

    /**
     * @meta Return InlineQuery Details.
     * @param string $select Fill With (id,query).
     * @return mixed
     */
    public function InlineQuery($select)
    {
        switch ($select) {
            case 'id';
                $return = $this->Data['inline_query']['id'];
                break;
            case 'query';
                $return = $this->Data['inline_query']['query'];
                break;
        }
        return $return;
    }

    /**
     * @return string Updates Type ['callback_query','inline_query'].
     */
    public function GetUpdateType()
    {
        if (isset($this->Data['callback_query'])) {
            return self::CALLBACK_QUERY;
        }
        if (isset($this->Data['inline_query'])) {
            return self::INLINE_QUERY;
        }
    }

    /**
     * @param mixed $callbackid Put CallBack ChatID
     * @param string $text Write Message Body.
     * @param boolean $alert Show Alert Dialog.
     */
    function AnswerCallback($callbackid, $text, $alert = false)
    {
        BOT('answerCallbackQuery', [
            'callback_query_id' => $callbackid,
            'text' => $text,
            'show_alert' => $alert
        ]);
    }

    /**
     * @param mixed $id Put inlineQuery ID.
     * @param mixed $json Result As json.
     */
    function AnswerInline($id, $json)
    {
        BOT('answerInlineQuery', [
            'inline_query_id' => $id,
            'results' => json_encode($json)
        ]);
    }

    /**
     * @meta Pin A Message In Chat.
     * @param mixed $chat Where You Want To Pin Message.
     * @param int $msgid Identifier Of A Message To Pin.
     * @param boolean $notification Pass True, if it is not necessary to send a notification to all chat members about the new pinned message. Notifications are always disabled in channels and private chats.
     */
    function PinMessage($chat, $msgid, $notification = false)
    {
        BOT('pinChatMessage', [
            'chat_id' => $chat,
            'message_id' => $msgid,
            'disable_notification' => $notification
        ]);
    }

    /**
     * @meta Clear Pinned Message In Chat.
     * @param mixed $chat Where You Want To Remove Pinned Message.
     * @param int $msgid Identifier Of A Message To Pin.
     */
    function UnPinMessage($chat, $msgid)
    {
        BOT('unpinAllChatMessages', [
            'chat_id' => $chat,
            'message_id' => $msgid
        ]);
    }

    /**
     * @meta Remove All Pinned Messages In Chat.
     * @param mixed $chat Where You Want To Remove Pinned Message.
     */
    function UnPinAllChatMessages($chat)
    {
        BOT('unpinAllChatMessages', ['chat_id' => $chat]);
    }
}
