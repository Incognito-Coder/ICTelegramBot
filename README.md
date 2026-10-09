# ICTelegramBot

[![PHP Version](https://img.shields.io/badge/php-%3E%3D7.4-8892BF.svg)](https://php.net/)
[![Telegram Bot API](https://img.shields.io/badge/Telegram%20Bot%20API-7.x%20--%208.3%2B-blue.svg)](https://core.telegram.org/bots/api)
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE.md)

A lightweight, zero-dependency, modern PHP library for building Telegram bots based on the official [Telegram Bot API](https://core.telegram.org/bots/api).

---

## ✨ Features

- **Zero External Dependencies**: Single-file plug-and-play architecture with clean native cURL implementation.
- **Latest Bot API Support (8.3+)**:
  - **Telegram Stars & Monetization**: `sendPaidMedia`, `sendInvoice`, `createInvoiceLink` (including recurring Star subscriptions), `refundStarPayment`, and `getStarTransactions`.
  - **Reactions & Message Effects**: `setMessageReaction`, animated effects (`message_effect_id`), and rich replies.
  - **Replies 2.0 & Quotes**: Support for `reply_parameters` with quoting (`quote`, `quote_position`) and `link_preview_options`.
  - **Forum Topics**: Full management for supergroups (`createForumTopic`, `editForumTopic`, `closeForumTopic`, `unpinAllForumTopicMessages`, etc.).
  - **Verification API (8.2+)**: `verifyUser`, `verifyChat`, `removeUserVerification`, `removeChatVerification`.
  - **Gifts (8.2 - 8.3+)**: `sendGift`, `getAvailableGifts` with channel and upgrade options.
  - **Chat Moderation & Administration**: Member restriction, bans, custom admin titles, invite link management, and batch deletion (`deleteMessages`).
- **Webhook Security**: Built-in secret token validation with `VerifyWebhookSecret()`.
- **Proxy Support**: Native HTTP, SOCKS4, and SOCKS5 proxy routing with authentication.
- **Auto Local File Uploads**: Automatically converts local file paths to `CURLFile` for photos, videos, audio, voice notes, animations, and documents.
- **Smart Update Parsing**: Built-in extractors for chat IDs, user IDs, topics, media IDs, and command arguments (`IsCommand`, `GetCommand`, `GetCommandArgs`).
- **Interactive Keyboard Builders**: Clean fluent builders for both `InlineKeyboardMarkup` and `ReplyKeyboardMarkup`.
- **Full Backward Compatibility**: 100% compatible with existing ICTelegramBot v1.x codebases.

---

## 🚀 Installation & Getting Started

### Option 1: Via Composer (Recommended)

```bash
composer require incognito-coder/ic-telegram-bot
```

```php
require_once 'vendor/autoload.php';

use TelegramBot\ICBot;

$bot = new ICBot('YOUR_BOT_TOKEN');
```

### Option 2: Direct Download

Download [`ICTelegramBot.php`](file:///e:/Projects/Telegram/ICTelegramBot.php) directly into your project:

```php
use TelegramBot\ICBot;

if (!file_exists('ICTelegramBot.php')) {
    copy('https://raw.githubusercontent.com/Incognito-Coder/ICTelegramBot/main/ICTelegramBot.php', 'ICTelegramBot.php');
}
require_once 'ICTelegramBot.php';

$bot = new ICBot();
$bot->Initialize('YOUR_BOT_TOKEN');
```

---

## ⚙️ Initialization & Proxy Setup

You can initialize with credentials or configure proxy connections directly:

```php
use TelegramBot\ICBot;

$bot = new ICBot();

// Basic initialization
$bot->Initialize('123456789:ABCdefGhIJKlmNoPQRsTUVwxyZ');

// With SOCKS5 / HTTP Proxy & Authentication
$bot->Initialize(
    '123456789:ABCdefGhIJKlmNoPQRsTUVwxyZ',
    '127.0.0.1:1080',       // Proxy address
    'username:password',     // Proxy auth (or null)
    'SOCKS5'                 // Proxy type: 'HTTP', 'SOCKS4', 'SOCKS5'
);

// Or configure a custom/local Bot API server
$bot->Initialize('TOKEN', null, null, null, 'http://localhost:8081');
```

---

## 📁 Examples Included

Complete runnable examples are provided in the [`examples/`](file:///e:/Projects/Telegram/examples/) folder:
- [**`examples/echo_bot.php`**](file:///e:/Projects/Telegram/examples/echo_bot.php): Webhook bot with command handling and secret token security.
- [**`examples/polling_bot.php`**](file:///e:/Projects/Telegram/examples/polling_bot.php): CLI long-polling runner for testing without a domain.
- [**`examples/keyboard_bot.php`**](file:///e:/Projects/Telegram/examples/keyboard_bot.php): Inline keyboards, mini apps, callbacks, and custom reply menus.

---

## 📖 Usage Examples

### 1. Webhook Echo Bot with Secret Token Security

```php
use TelegramBot\ICBot;

require_once 'ICTelegramBot.php';

$bot = new ICBot('YOUR_BOT_TOKEN');

// Validate X-Telegram-Bot-Api-Secret-Token
if (!$bot->VerifyWebhookSecret('MY_SECRET_KEY')) {
    http_response_code(403);
    exit('Unauthorized');
}

$chatId = $bot->GetChatID();
$text   = $bot->GetText();

if ($text === '/start') {
    $bot->SendMessage($chatId, "👋 Hello *{$bot->GetFirstname()}*! Welcome to ICTelegramBot.", 'Markdown');
} else if (!empty($text)) {
    $bot->SendMessage($chatId, "You said: " . htmlspecialchars($text), 'HTML');
}
```

### 2. Command Handling with Arguments

```php
if ($bot->IsCommand()) {
    $command = $bot->GetCommand();
    $args    = $bot->GetCommandArgs();
    $argStr  = $bot->GetCommandArgsString();

    switch ($command) {
        case 'kick':
            if (!empty($args[0])) {
                $targetUser = intval($args[0]);
                $bot->BanChatMember($bot->GetChatID(), $targetUser);
                $bot->SendMessage($bot->GetChatID(), "Banned user: {$targetUser}");
            }
            break;

        case 'say':
            $bot->SendMessage($bot->GetChatID(), $argStr);
            break;
    }
}
```

### 3. Sending Media (Local Files, URLs, or File IDs)

```php
// Local file automatically uploaded via multipart/form-data
$bot->SendPhoto($chatId, 'images/banner.jpg', 'Check this photo out!', 'HTML');

// Using Telegram File ID or URL
$bot->SendVideo($chatId, 'https://example.com/demo.mp4', 'Tutorial Video');

// Sending Voice Message or Audio
$bot->SendVoice($chatId, 'audio/note.ogg');
$bot->SendAudio($chatId, 'music/track.mp3', null, null, 180, 'Artist Name', 'Song Title');
```

### 4. Interactive Keyboards

```php
// Inline Keyboard Builder
$keyboard = $bot->BuildInlineKeyboard([
    [
        $bot->InlineButton('🌐 Open Website', ['url' => 'https://telegram.org']),
        $bot->InlineButton('👍 Like', ['callback_data' => 'like_post']),
    ],
    [
        $bot->InlineButton('🚀 Launch Mini App', ['web_app' => ['url' => 'https://app.example.com']]),
    ]
]);

$bot->SendMessage($chatId, "Choose an action below:", null, null, null, null, $keyboard);

// Reply Keyboard Builder
$replyMarkup = $bot->BuildReplyKeyboard([
    ['Yes', 'No'],
    [$bot->ReplyButton('📱 Send Phone Number', ['request_contact' => true])],
], ['input_field_placeholder' => 'Make a choice...']);

$bot->SendMessage($chatId, "Please choose:", null, null, null, null, $replyMarkup);
```

### 5. Telegram Stars & Payments

```php
// Send an invoice for Telegram Stars (XTR)
$bot->SendInvoice(
    $chatId,
    'Premium VIP Access',
    'Get 1 month access to premium features',
    'payload_vip_month',
    'XTR',
    [['label' => 'VIP Access', 'amount' => 50]] // 50 Telegram Stars
);

// Create recurring Star subscription link (Bot API 8.0+)
$link = $bot->CreateInvoiceLink(
    'Monthly Channel Subscription',
    'VIP Channel Membership',
    'payload_sub',
    'XTR',
    [['label' => 'Subscription', 'amount' => 100]],
    ['subscription_period' => 2592000] // 30 days in seconds
);
```

### 6. Message Reactions & Forum Topics

```php
// Add emoji reaction (Bot API 7.0+)
$bot->SetMessageReaction($chatId, $bot->MessageID(), '🔥');

// Create and manage Forum Topics in supergroups
$topic = $bot->CreateForumTopic($chatId, 'Announcements');
$threadId = $topic->result->message_thread_id;

$bot->SendMessage($chatId, 'Welcome to the Announcements topic!', null, null, null, null, null, [
    'message_thread_id' => $threadId
]);
```

### 7. Universal Endpoint Calling

Call any new or upcoming Telegram API method dynamically:

```php
$response = $bot->Endpoint('sendChatAction', [
    'chat_id' => $chatId,
    'action'  => 'typing',
]);
```

---

## 🗂️ Core Methods Reference

| Category | Method | Description |
|---|---|---|
| **Setup & Updates** | `Initialize($token, $proxy, $auth, $type, $apiUrl)` | Configure bot and proxy |
| | `SetWebHook($url, $options)` | Register webhook endpoint |
| | `DeleteWebHook($dropUpdates)` | Remove webhook |
| | `VerifyWebhookSecret($expectedToken)` | Validate secret token header |
| | `GetUpdates($options)` | Long-polling updates |
| | `GetMe()` | Get bot info (`User` object) |
| **Messaging** | `SendMessage($chat, $text, $parse, $preview, $notify, $reply, $kbd, $extra)` | Send text message |
| | `CopyMessage($chat, $from, $msgid, ...)` | Copy any message |
| | `ForwardMessage($chat, $from, $msgid, ...)` | Forward message |
| | `EditMessage($chat, $msgid, $text, ...)` | Edit message text |
| | `DeleteMessage($chat, $msgid)` | Delete single message |
| | `DeleteMessages($chat, $msgids)` | Bulk delete messages |
| | `SetMessageReaction($chat, $msgid, $reaction)` | React to message with emoji |
| **Media** | `SendPhoto($chat, $photo, ...)` | Send photo |
| | `SendVideo($chat, $video, ...)` | Send video |
| | `SendAnimation($chat, $animation, ...)` | Send GIF / animation |
| | `SendAudio($chat, $audio, ...)` | Send audio file |
| | `SendVoice($chat, $voice, ...)` | Send voice message |
| | `SendDocument($chat, $doc, ...)` | Send document/file |
| | `SendMediaGroup($chat, $media)` | Send photo/video album |
| | `SendPaidMedia($chat, $stars, $media)` | Send Telegram Stars paid media |
| **Payments & Stars** | `SendInvoice($chat, $title, $desc, $payload, $currency, $prices)` | Send payment / Star invoice |
| | `CreateInvoiceLink($title, $desc, $payload, $currency, $prices)` | Create payment / subscription link |
| | `AnswerPreCheckoutQuery($id, $ok, $err)` | Confirm / reject order |
| | `RefundStarPayment($user, $charge_id)` | Refund Telegram Star payment |
| **Moderation** | `BanChatMember($chat, $user, $until, $revoke)` | Ban member |
| | `UnbanChatMember($chat, $user)` | Unban member |
| | `RestrictChatMember($chat, $user, $perms, ...)` | Restrict permissions |
| | `PromoteChatMember($chat, $user, $rights)` | Promote admin |
| | `PinMessage($chat, $msgid, $notify)` | Pin message |
| | `UnPinMessage($chat, $msgid)` | Unpin message |
| **Forum Topics** | `CreateForumTopic($chat, $name, ...)` | Create forum topic |
| | `EditForumTopic($chat, $threadId, $name, ...)` | Rename forum topic |
| | `CloseForumTopic($chat, $threadId)` | Close forum topic |
| | `ReopenForumTopic($chat, $threadId)` | Reopen forum topic |
| **Verification** | `VerifyUser($user_id, $desc)` | Verify user (Bot API 8.2) |
| | `VerifyChat($chat_id, $desc)` | Verify chat (Bot API 8.2) |
| **Inspection** | `GetChatID()`, `FromID()`, `MessageID()` | Extract current IDs |
| | `GetText()`, `GetCaption()`, `GetUsername()` | Extract message data |
| | `IsCommand()`, `GetCommand()`, `GetCommandArgs()` | Inspect bot commands |

---

## 📄 License

The MIT License (MIT). Please see the [License File](LICENSE.md) for more details.
