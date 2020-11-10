<?php

$appid = 'wx32958aa1d8e5dad9';
$secret = '354d410273d139e5e219d46c26cbfca5';


$token_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";
$token_res = https_request($token_url);
$token_res = json_decode($token_res, true);
$token = $token_res['access_token'];


$ticket_url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token={$token}&type=jsapi";
$ticket_res = https_request($ticket_url);
$ticket_res = json_decode($ticket_res, true);
$ticket = $ticket_res['ticket'];

/**
 * 模拟 http 请求
 * @param  String $url  请求网址
 * @param  Array  $data 数据
 */
function https_request($url, $data = null){
    // curl 初始化
    $curl = curl_init();

    // curl 设置
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);

    // 判断 $data get  or post
    if ( !empty($data) ) {
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    // 执行
    $res = curl_exec($curl);
    curl_close($curl);
    return $res;
}



// 进行sha1签名
$timestamp = time();
$nonceStr = createNonceStr();

// 注意 URL 建议动态获取(也可以写死).
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; // 调用JSSDK的页面地址
// $url = $_SERVER['HTTP_REFERER']; // 前后端分离的, 获取请求地址(此值不准确时可以通过其他方式解决)

$str = "jsapi_ticket={$ticket}&noncestr={$nonceStr}&timestamp={$timestamp}&url={$url}";
$sha_str = sha1($str);


function createNonceStr($length = 16) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
      $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
}


?>





<html>
<head>
  <title></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="./css/index2.css">
</head>
<body>
　　

    <div class="wrapper">
        <!-- <audio src="./beijin.mp3" id="audioPlay" autoplay="autoplay" loop="loop"></audio> -->
        <div class="top"></div>
        <div class="left"></div>
        <div class="content">
            <div class="btnContent">
                <p class="state"></p>
                <div class="button playRecord">播放录音</div>
                <div class="button goRecord"><a href="http://lh.22do.cn/H5Record/index.php">我也想录音</a></div>
            </div>
        </div>
    </div>

    
    <script type="text/javascript" src="https://res.wx.qq.com/open/js/jweixin-1.6.0.js"></script>

    <script type="text/javascript">
        
        // 配置接口成功
        wx.config({
            debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
            appId: '<? echo $appid ?>', // 必填，公众号的唯一标识
            timestamp: <? echo $timestamp ?>, // 必填，生成签名的时间戳
            nonceStr: '<? echo $nonceStr ?>', // 必填，生成签名的随机串
            signature: '<? echo $sha_str ?>',// 必填，签名
            jsApiList: [ // 必填，需要使用的JS接口列表
                'startRecord', // 开始录音接口
                'stopRecord', // 停止录音接口
                'uploadVoice', // 上传录音接口
                'downloadVoice', // 下载语音接口
                'playVoice', // 播放录音的接口
                // 'updateAppMessageShareData', // 自定义“分享给朋友”
                // 'updateTimelineShareData' // 自定义“分享到朋友圈”
                ] 
        });

        // 获取当前url参数的方法
        function getQueryString(name) { 
            var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i"); 
            var r = window.location.search.substr(1).match(reg); 
            if (r != null){ return decodeURI(r[2])}//中文转码
            return null; 
        } 

        localId = getQueryString('localId');
        wx.ready(function(){
            console.log('接口配置成功');
            var audio = document.getElementById("audioPlay");
 　　　　    audio.play();

            localId = getQueryString('localId');
            
        });


        var localId; // 微信录音ID
        var playRecord = document.getElementsByClassName('playRecord')[0];
        var myAudio = document.getElementById('audioPlay');
        var state = document.getElementsByClassName('state')[0];

        
        // 监听点击播放语音点击事件
        playRecord.addEventListener('click', function() {
          if(localId) { // 判断是否录到音了
            // 监听播放录音完毕
            wx.onVoicePlayEnd({
                success: function (res) {
                    state.innerText = '录音播放完毕';
                }
            });

            state.innerText = '播放录音中...';
            wx.playVoice({
                localId: localId // 需要播放的音频的本地ID，由stopRecord接口获得
            });     
          }else {
            state.innerText = '请重新刷新一下';
          }
         
        })

    
  </script>
</body>
</html>