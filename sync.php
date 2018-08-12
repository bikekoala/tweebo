<?PHP
require 'vendor/autoload.php';

// read config
$envFile = __DIR__ . '/.env';
if ( ! file_exists($envFile)) {
    output($envFile . ' file not found');
}
$env = parse_ini_file($envFile);

$storagePath = __DIR__ . '/storage';
$recordFile = $storagePath . '/lasttimestamp';
$pictureFileSuffix = $storagePath . '/img_';

// fetch one tweet
$twitter = new Twitter(
    $env['TWITTER_CONSUMER_KEY'],
    $env['TWITTER_CONSUMER_SECRET'],
    $env['TWITTER_ACCESS_TOKEN'],
    $env['TWITTER_ACCESS_TOKEN_SECRET']
);
$statuses = $twitter->load(Twitter::ME, 1);
$status = current($statuses);

$lastTime = (int) @file_get_contents($recordFile);
if (strtotime($status->created_at) <= $lastTime) {
    output('no tweets');
}

$urlPrefix = 'https://t.co';
if (false !== ($urlPos = strpos($status->text, $urlPrefix))) {
    $status->text = trim(substr($status->text, 0, $urlPos));
}
$text = trim($status->text);
$mediaUrl = $status->entities->media[0]->media_url ?? '';
$statusUrl = sprintf(
    'https://twitter.com/%s/status/%s',
    $env['TWITTER_USER_NAME'],
    $status->id_str
);

// share to weibo
$client = new SaeTClientV2(
    $env['WEIBO_APP_KEY'],
    $env['WEIBO_APP_SECRET'],
    $env['WEIBO_ACCESS_TOKEN']
);
$message = $text . ' ' . $statusUrl;
$picture = false;
if ($mediaUrl) {
    $picture = $pictureFileSuffix . $status->id_str;
    file_put_contents($picture, file_get_contents($mediaUrl));
}
$ret = $client->share($message, $picture);
if ( ! empty($ret['text'])) {
    file_put_contents($recordFile, strtotime($status->created_at));
    output(sprintf('%s-%s', $status->id_str, $ret['text']));
} else {
    output($ret);
}

/**
 * output
 *
 * @param string|array $message
 * @param bool $isExit
 * @return void
 */
function output($message, $isExit = true) {
    if (is_array($message)) {
        $message = json_encode($message);
    }
    echo sprintf("%s\t%s\n", date('Y-m-d H:i:s'), $message);

    if ($isExit) exit;
}
