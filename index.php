<?php

$appid = 'wx7790ede3938abf15';
$secret = '20ae51fe7481a749f6b45eece70615aa';


// 获取token
$token_data = file_get_contents('wechat_token.txt');
if (!empty($token_data)) {
    $token_data = json_decode($token_data, true);
}


$time  = time() - $token_data['time'];
if ($time > 3600) {
    $token_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";
    $token_res = https_request($token_url);
    $token_res = json_decode($token_res, true);
    $token = $token_res['access_token'];
 
    $data = array(
        'time' =>time(),
        'token' =>$token
    );
    // var_dump($data);
    // exit;
    $res = file_put_contents('wechat_token.txt', json_encode($data));
    if ($res) {
        // echo '更新 token 成功';
    }
} else {
     $token = $token_data['token'];
}




// 获取ticket
$ticket_data = file_get_contents('wechat_ticket.txt');
if (!empty($ticket_data)) {
    $ticket_data = json_decode($ticket_data, true);
}

$time  = time() - $ticket_data['time'];
if ($time > 3600) {
    $ticket_url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token={$token}&type=jsapi";
    $ticket_res = https_request($ticket_url);
    $ticket_res = json_decode($ticket_res, true);
    $ticket = $ticket_res['ticket'];

    $data = array(
        'time'    =>time(),
        'ticket'  =>$ticket
    );
    $res = file_put_contents('wechat_ticket.txt', json_encode($data));
    if ($res) {
        // echo '更新 ticket 成功';
    }
} else {
    $ticket = $ticket_data['ticket'];
}

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
    <link rel="stylesheet" href="./index.css">
</head>
<body>
　　
    <div class="wrapper">
      <div class="content">
        <p class="prompt">温馨提示：只能录制一分钟</p>
        <p class="countdown"></p>
      </div>
      <audio src="./beijin.mp3" id="audioPlay" autoplay="autoplay" loop="loop"></audio>
      <div class="record">
        <div class="recordStart"></div>
        <div class="recordStop"></div>
      </div>
      <div class="state">长按开始</div>
      <div class="playRecord">试听录音</div>    
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
                'scanQRCode',
                'startRecord', // 开始录音接口
                'stopRecord', // 停止录音接口
                'uploadVoice', // 上传录音接口
                'downloadVoice', // 下载语音接口
                'playVoice', // 播放录音的接口
                // 'updateAppMessageShareData', // 自定义“分享给朋友”
                // 'updateTimelineShareData' // 自定义“分享到朋友圈”
                ] 
        });

        wx.ready(function(){
            console.log('接口配置成功');
            var audio = document.getElementById("audioPlay");
 　　　　    audio.play();

            // // 分享给朋友
            // wx.updateAppMessageShareData({ 
            //  title: '一起录个音', // 分享标题
            //  desc: '嘿，小明向你说个悄悄话', // 分享描述
            //  link: 'http://lh.22do.cn/', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
            //  imgUrl: './start.png', // 分享图标
            //  success: function () {
            //    // 设置成功
            //    alert('分享成功')
            //  }
            // })

            // // 分享到朋友圈
            // wx.updateTimelineShareData({ 
            //   title: '一起录个音', // 分享标题
            //   link: 'http://lh.22do.cn/', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
            //   imgUrl: './start.png', // 分享图标
            //   success: function () {
            //     // 设置成功
            //     alert('分享成功')
            //   }
            // })

        });


        var localId, START, END, luyintime;
        var record = document.getElementsByClassName('record')[0];
        var playRecord = document.getElementsByClassName('playRecord')[0];
        var myAudio = document.getElementById('audioPlay');
        var state = document.getElementsByClassName('state')[0];
        record.addEventListener('touchstart', function() {
          recordStart.style.display = 'none';
          recordStop.style.display = 'block';

          state.innerText = '正在录制中...'
          startCountTime();

          wx.startRecord({
              success: function(){
                  START = new Date().getTime();
                  wx.onVoiceRecordEnd({
                      // 录音时间超过一分钟没有停止的时候会执行 complete 回调
                      complete: function (res) {
                          alert('最多只能录制一分钟');
                          localId = res.localId;
                          uploadluyin(localId,60000);
                      }
                  });
              },
              cancel: function () {
                  alert('用户拒绝授权录音');
                  return false;
              }
          });
        })
        
        record.addEventListener('touchend', function() {
          recordStart.style.display = 'block';
          recordStop.style.display = 'none';


          state.innerText = '录制完毕';
          stopCountTime();
          
          END = new Date().getTime();
          //录音时间
          luyintime=END - START;
          if(luyintime < 2000){
              END = 0;
              START = 0;
              wx.stopRecord({});
              alert('录音时间不能少于2秒');
              return false;
              //小于300ms，不录音
          }else {
              wx.stopRecord({
                  success: function (res) {
                      localId = res.localId;
                      uploadluyin(localId,luyintime);
                      alert(localId);
                  }
              });
          }
        })


        function uploadluyin(localId,luyintime) {
          wx.uploadVoice({
              localId: localId, // 需要上传的音频的本地ID，由stopRecord接口获得
              isShowProgressTips: 1, // 默认为1，显示进度提示
              success: function (res) {
                  state.innerText = '长按开始';
                  var serverId = res.serverId; // 返回音频的服务器端ID
                  console.log(serverId);
                  // $.post("/home/xishanluyin/scyuyin", {
                  //             "serverId": serverId,
                  //             "luyintime": luyintime
                  //         },
                  //         function (data) {
                  //             if (data.success == 1) {
                  //                 alert('录音成功');
                  //             } else {
                  //                 alert(data.msg);
                  //             }
                  //         }, "json");
              }
          })
        }

        // 播放语音
        playRecord.addEventListener('click', function() {
          console.log('播放录音了');
          wx.playVoice({
            localId: localId // 需要播放的音频的本地ID，由stopRecord接口获得
          });
        })

    

        // 倒计时60秒开始
        var countdown = document.getElementsByClassName('countdown')[0];
        var timeId; 
        function startCountTime(seconds,speed) {
          var seconds = 60;
          var speed = 1000;
          var span = document.createElement('span');
          countdown.appendChild(span);
          span.innerHTML = '录音倒计时:' + seconds;
          timeId = setInterval(function() {
                  if(seconds == 0){
                      clearInterval(timeId);
                  };                        
                  seconds --;
                  span.innerHTML = '录音倒计时:' + seconds;
          }, speed);
        }

        // 停止倒计时
        function stopCountTime() {
          clearTimeout(timeId); 
          var childs = countdown.childNodes; 
          for(var i = 0; i < childs.length; i++) { 
            countdown.removeChild(childs[i]); 
          }
        }

        var recordStart = document.getElementsByClassName('recordStart')[0];
        var recordStop = document.getElementsByClassName('recordStop')[0];


  </script>
</body>
</html>