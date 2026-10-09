<?php
/**
 * Example 3: Interactive Keyboards & Callbacks
 */

require_once __DIR__ . '/../ICTelegramBot.php';

use TelegramBot\ICBot;

$token = 'YOUR_BOT_TOKEN';
$bot = new ICBot($token);

$chatId = $bot->GetChatID();
$text   = $bot->GetText();
$updateType = $bot->GetUpdateType();

// 1. Handle Callback Query from Inline Keyboard
if ($updateType === ICBot::CALLBACK_QUERY) {
    $callbackId = $bot->CallBackQuery('id');
    $data       = $bot->CallBackQuery('data');
    $msgId      = $bot->CallBackQuery('msgid');

    switch ($data) {
        case 'btn_like':
            $bot->AnswerCallback($callbackId, '❤️ Thanks for liking!');
            $bot->EditMessage($chatId, $msgId, 'You liked this post! ❤️');
            break;

        case 'btn_alert':
            $bot->AnswerCallback($callbackId, '⚠️ This is a popup alert message!', true);
            break;

        default:
            $bot->AnswerCallback($callbackId, 'Clicked: ' . $data);
            break;
    }
    exit;
}

// 2. Handle Text Commands to Show Keyboards
if ($text === '/inline') {
    $inlineKbd = $bot->BuildInlineKeyboard([
        [
            $bot->InlineButton('👍 Like', ['callback_data' => 'btn_like']),
            $bot->InlineButton('⚠️ Show Alert', ['callback_data' => 'btn_alert']),
        ],
        [
            $bot->InlineButton('🌐 Official Website', ['url' => 'https://telegram.org']),
            $bot->InlineButton('🚀 Mini App', ['web_app' => ['url' => 'https://telegram.org/faq']]),
        ]
    ]);

    $bot->SendMessage($chatId, "Here is an *Inline Keyboard*:", 'Markdown', null, null, null, $inlineKbd);

} elseif ($text === '/reply') {
    $replyKbd = $bot->BuildReplyKeyboard([
        ['Option 1', 'Option 2'],
        [
            $bot->ReplyButton('📞 Share Contact', ['request_contact' => true]),
            $bot->ReplyButton('📍 Share Location', ['request_location' => true]),
        ],
        ['/remove']
    ], ['input_field_placeholder' => 'Select an option...']);

    $bot->SendMessage($chatId, "Here is a *Reply Keyboard*:", 'Markdown', null, null, null, $replyKbd);

} elseif ($text === '/remove') {
    $removeKbd = $bot->ReplyKeyboardRemove();
    $bot->SendMessage($chatId, 'Reply keyboard removed.', null, null, null, null, $removeKbd);
}
