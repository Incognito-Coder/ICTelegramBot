<?php
/**
 * Example 2: Modern Long Polling Bot (CLI)
 *
 * Run this directly from command line (ideal for local testing or 24/7 background daemons):
 * php examples/polling_bot.php
 */

require_once __DIR__ . '/../ICTelegramBot.php';

use TelegramBot\ICBot;

$token = 'YOUR_BOT_TOKEN'; // Replace with your bot token
$bot = new ICBot($token);

// Optional: Configure proxy if needed
// $bot->Initialize($token, '127.0.0.1:1080', null, 'SOCKS5');

echo "========================================\n";
echo " Starting Modern Long Polling Bot\n";
echo " Press Ctrl+C to exit gracefully\n";
echo "========================================\n";

/**
 * Modern Event-Driven Long Polling
 *
 * Automatically handles:
 * - Webhook removal & pending update backlog management
 * - Recursive update conversion & state loading into ICBot
 * - Incremental offset management & confirmation
 * - Network reconnection and rate-limit (429 retry_after) backoff
 * - Cross-platform graceful shutdown on Ctrl+C (Windows & POSIX)
 */
$bot->StartPolling(function (ICBot $bot, array $update) {
    $chatId = $bot->GetChatID();
    $text   = $bot->GetText();
    $fromId = $bot->FromID();

    if (empty($chatId)) {
        return;
    }

    echo "Received update #{$update['update_id']} from user {$fromId} in chat {$chatId}\n";

    if ($bot->IsCommand()) {
        $command = $bot->GetCommand();
        $args    = $bot->GetCommandArgs();

        switch ($command) {
            case 'start':
                $bot->SendMessage(
                    $chatId,
                    "👋 Hello <b>{$bot->GetFirstname()}</b>!\nLong Polling is running smoothly on ICTelegramBot.",
                    'HTML'
                );
                break;

            case 'ping':
                $bot->SendMessage($chatId, '🏓 Pong!');
                break;

            case 'dice':
                $bot->SendDice($chatId, ICBot::DICE_DEFAULT);
                break;

            case 'stop':
                $bot->SendMessage($chatId, '🛑 Stopping bot daemon...');
                $bot->StopPolling();
                break;

            default:
                $bot->SendMessage($chatId, "Unknown command: /{$command}");
                break;
        }
    } elseif (!empty($text)) {
        $bot->SendMessage($chatId, "Echo: " . htmlspecialchars($text), 'HTML');
    }
}, [
    'timeout'              => 30,    // 30s HTTP long polling timeout
    'limit'                => 100,   // Max updates per batch (1-100)
    'delete_webhook'       => true,  // Automatically clears webhook to avoid 409 conflict
    'drop_pending_updates' => false, // Set to true to drop old backlog on startup
    'on_error'             => function ($error, $bot) {
        echo "[ERROR] {$error}\n";
    },
    'on_stop'              => function ($bot) {
        echo "Polling stopped cleanly.\n";
    },
]);

/**
 * Alternative: Stream-Based Long Polling via PHP Generator:
 *
 * foreach ($bot->PollUpdates(['timeout' => 30]) as $update) {
 *     $chatId = $bot->GetChatID();
 *     $text   = $bot->GetText();
 *     if ($text === '/ping') {
 *         $bot->SendMessage($chatId, 'Pong!');
 *     }
 * }
 */

