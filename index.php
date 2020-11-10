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
    
    <link rel="stylesheet" href="./css/index.css">
    
</head>
<body>
　　

    <div class="wrapper">
        <audio src="./beijin.mp3" id="audioPlay" autoplay="autoplay" loop="loop"></audio>
        <div class="top">
            <p class="countdown"></p>
            <p class="state"></p>
        </div>
        <div class="content">
            <img class="postcard" src="./images/postcard1.png" alt="">
            <p class="prompt">温馨提示：只能录制一分钟</p>
        </div>
        <div class="bottom">
            <div class="bottomContent">
                <div class="promptContent">
                    <!-- <p class="prompt">温馨提示：只能录制一分钟</p> -->
                    <!-- <p class="countdown"></p> -->
                </div>
                <div class="btnContent">
                    <!-- <p class="state"></p> -->
                    <div class="button record">点击录音</div>
                    <div class="button stopRecord">结束录音</div>
                    <div class="button playRecord">试听录音</div>
                    <div class="button shareTo">分享声音明信片</div>
                </div>
            </div>
        </div>
        <div class="layer">
            <img class="sharePhoto" src="./images/share.png" alt="">
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
                'updateAppMessageShareData', // 自定义“分享给朋友”
                'updateTimelineShareData' // 自定义“分享到朋友圈”
                ] 
        });

    
        wx.ready(function(){
            console.log('接口配置成功');
            var audio = document.getElementById("audioPlay");
 　　　　    audio.play();       
        });


        var localId, START, END, luyintime;
        var record = document.getElementsByClassName('record')[0];
        var stopRecord = document.getElementsByClassName('stopRecord')[0];
        var playRecord = document.getElementsByClassName('playRecord')[0];
        var myAudio = document.getElementById('audioPlay');
        var state = document.getElementsByClassName('state')[0];
        var share = document.getElementsByClassName('shareTo')[0];
        var layer = document.getElementsByClassName('layer')[0];


        layer.addEventListener('touchstart', function() { 
            layer.style.display = 'none';
        })
        // 点击分享
        share.addEventListener('touchstart', function() { 
            layer.style.display = 'block';
            wx.updateAppMessageShareData({ 
                title: '以声会面，以声传情', // 分享标题
                desc: '', // 分享描述
                link: `http://lh.22do.cn/H5Record/index2.php?localId=${localId}`, // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
                imgUrl: 'http://lh.22do.cn/H5Record/images/cover.jpg', // 分享图标
                success: function () {
                   
                }
            })
            
            // 自定义“分享到朋友圈”及“分享到QQ空间”按钮的分享内容（1.4.0）
            wx.updateTimelineShareData({ 
                title: '以声会面，以声传情', // 分享标题
                link: `http://lh.22do.cn/H5Record/index2.php?localId=${localId}`, // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
                imgUrl: 'http://lh.22do.cn/H5Record/images/cover.jpg', // 分享图标
                success: function () {

                }
            })
        })

        
        // 监听开始录音点击事件
        record.addEventListener('touchstart', function() {
          console.log(111);
          record.style.display = 'none';
          stopRecord.style.display = 'block';

          localId = '';  
          stopCountTime();
          startCountTime();
          state.innerText = '正在录音中...';

          wx.startRecord({
              success: function(){
                  START = new Date().getTime();
                  wx.onVoiceRecordEnd({
                      // 录音时间超过一分钟没有停止的时候会执行 complete 回调
                      complete: function (res) {
                          record.style.display = 'block';
                          stopRecord.style.display = 'none';
                          state.innerText = '录音完毕';
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
        
        // 监听点击结束录音点击事件
        stopRecord.addEventListener('touchstart', function() {
          record.style.display = 'block';
          stopRecord.style.display = 'none';
          stopCountTime();
          state.innerText = '录音完毕';
          
          END = new Date().getTime();
          //录音时间
          luyintime=END - START;
          if(luyintime < 2000){
              END = 0;
              START = 0;
              wx.stopRecord({});
              state.innerText = '录音时间不能少于2秒';
              return false;
              //小于300ms，不录音
          }else {
              wx.stopRecord({
                  success: function (res) {
                      localId = res.localId;
                      uploadluyin(localId,luyintime);
                    //   alert(localId);
                  }
              });
          }
        })


        function uploadluyin(localId,luyintime) {
          wx.uploadVoice({
              localId: localId, // 需要上传的音频的本地ID，由stopRecord接口获得
              isShowProgressTips: 1, // 默认为1，显示进度提示
              success: function (res) {
                  state.innerText = '录音上传完毕，点击可重新录音';
                  var serverId = res.serverId; // 返回音频的服务器端ID
              }
          })
        }

        // 监听点击播放语音点击事件
        playRecord.addEventListener('click', function() {
          stopCountTime();
          if(localId) { // 判断是否录到音了
            // 监听播放录音完毕
            wx.onVoicePlayEnd({
                success: function (res) {
                    state.innerText = '试听录音播放完毕,可点击录音重新录音';
                }
            });

            state.innerText = '播放试听录音中...';
            stopCountTime();
            wx.playVoice({
                localId: localId // 需要播放的音频的本地ID，由stopRecord接口获得
            });     
          }else {
            state.innerText = '您没有录音，请录音';
          }
         
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
                      return;
                  };                        
                  seconds --;
                  span.innerHTML = '录音倒计时:' + seconds;
          }, speed);
        }

        // 停止倒计时
        function stopCountTime() {
          clearTimeout(timeId); 
          var childs = countdown.childNodes; 
          if(childs) {
            for(var i = 0; i < childs.length; i++) { 
                countdown.removeChild(childs[i]); 
            }
          }
        }

  </script>
</body>
</html>