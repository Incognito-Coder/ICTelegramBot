<?php
/**
 * Example 1: Webhook Echo Bot
 *
 * Place this file on your public HTTPS web server and register it with:
 * $bot->SetWebHook('https://yourdomain.com/webhook.php', ['secret_token' => 'YOUR_SECRET_KEY']);
 */

require_once __DIR__ . '/../ICTelegramBot.php';

use TelegramBot\ICBot;

// 1. Initialize Bot with your token
$token = 'YOUR_BOT_TOKEN';
$secretToken = 'OPTIONAL_SECRET_TOKEN'; // Set in SetWebHook

$bot = new ICBot($token);

// 2. Optional: Verify Telegram webhook secret token
if (!empty($secretToken) && !$bot->VerifyWebhookSecret($secretToken)) {
    http_response_code(403);
    exit('Unauthorized');
}

// 3. Extract incoming update information
$chatId = $bot->GetChatID();
$text   = $bot->GetText();

if (empty($chatId)) {
    exit('No chat ID detected.');
}

// 4. Handle commands or echo messages
if ($bot->IsCommand()) {
    $command = $bot->GetCommand();
    $args    = $bot->GetCommandArgs();

    switch ($command) {
        case 'start':
            $bot->SendMessage(
                $chatId,
                "👋 Hello *{$bot->GetFirstname()}*! Welcome to ICTelegramBot.\n\nSend any text message and I will echo it back to you.",
                'Markdown'
            );
            break;

        case 'help':
            $bot->SendMessage(
                $chatId,
                "<b>Available Commands:</b>\n/start - Start bot\n/help - Show this message\n/info - Show your account info",
                'HTML'
            );
            break;

        case 'info':
            $isPrem = $bot->IsPremium() ? 'Yes ⭐' : 'No';
            $bot->SendMessage(
                $chatId,
                "<b>Your Info:</b>\n" .
                "• ID: <code>{$bot->FromID()}</code>\n" .
                "• Name: {$bot->GetFullName()}\n" .
                "• Username: @{$bot->GetUsername()}\n" .
                "• Premium: {$isPrem}\n" .
                "• Language: {$bot->GetLanguageCode()}",
                'HTML'
            );
            break;

        default:
            $bot->SendMessage($chatId, "Unknown command: /{$command}");
            break;
    }
} elseif (!empty($text)) {
    // Echo the message back and react with a flame emoji
    $bot->SendMessage($chatId, "Echo: " . htmlspecialchars($text), 'HTML');
    $bot->SetMessageReaction($chatId, $bot->MessageID(), '🔥');
}
