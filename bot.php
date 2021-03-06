<? 
define('CALLBACK_API_CONFIRMATION_TOKEN', '0c4514e0'); // Ñòðîêà, êîòîðóþ äîëæåí âåðíóòü ñåðâåð 
define('VK_API_ACCESS_TOKEN', 'f93b8546c7c8801d5c9707ab82219b034d7a76cfd54090901c3d61c43e0510c90c764ce5bf38dfe4ac7ac'); // Êëþ÷ äîñòóïà ñîîáùåñòâà 

define('CALLBACK_API_EVENT_CONFIRMATION', 'confirmation'); // Òèï ñîáûòèÿ î ïîäòâåðæäåíèè ñåðâåðà 
define('CALLBACK_API_EVENT_MESSAGE_NEW', 'message_new'); // Òèï ñîáûòèÿ î íîâîì ñîîáùåíèè 
define('VK_API_ENDPOINT', 'https://api.vk.com/method/'); // Àäðåñ îáðàùåíèÿ ê API 
define('VK_API_VERSION', '5.89'); // Èñïîëüçóåìàÿ âåðñèÿ API 

$event = json_decode(file_get_contents('php://input'), true); 

switch ($event['type']) { 
  // Ïîäòâåðæäåíèå ñåðâåðà 
  case CALLBACK_API_EVENT_CONFIRMATION: 
    echo(CALLBACK_API_CONFIRMATION_TOKEN); 
    break; 
  // Ïîëó÷åíèå íîâîãî ñîîáùåíèÿ 
  case CALLBACK_API_EVENT_MESSAGE_NEW: 
    $message = $event['object']; 
    $peer_id = $message['peer_id'] ?: $message['user_id']; 
    send_message($peer_id, "Ïîæàëóéñòà, ïîäïèøèñü í ñîîáùåñòâî! (peer_id: {$peer_id})"); 
    echo('ok'); 
    break; 
  default: 
    echo('Unsupported event'); 
    break; 
} 

function send_message($peer_id, $message) { 
  api('messages.send', array( 
    'peer_id' => $peer_id, 
    'message' => $message, 
  )); 
} 

function api($method, $params) { 
  $params['access_token'] = VK_API_ACCESS_TOKEN; 
  $params['v'] = VK_API_VERSION; 
  $query = http_build_query($params); 
  $url = VK_API_ENDPOINT . $method . '?' . $query; 
  $curl = curl_init($url); 
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
  $json = curl_exec($curl); 
  $error = curl_error($curl); 
  if ($error) { 
    error_log($error); 
    throw new Exception("Failed {$method} request"); 
  } 
  curl_close($curl); 
  $response = json_decode($json, true); 
  if (!$response || !isset($response['response'])) { 
    error_log($json); 
    throw new Exception("Invalid response for {$method} request"); 
  } 
  return $response['response']; 
}
/?>
