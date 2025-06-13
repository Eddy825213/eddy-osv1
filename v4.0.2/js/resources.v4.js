/*
@ Resouces by ThanhDieu.Com
@ Designer V4.0.2
@ Tag: #thanhdieu, #thanhdieutv, #thanhdieudev
*/
// Cache blocked function
function _randomstring(length) {
  let result = '';
  let characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
  let charactersLength = characters.length;
  for (let i = 0; i < length; i++) {
      result += characters.charAt(Math.floor(Math.random() * charactersLength));
  }
  return result;
}
const a = 8;
const b = 15;
const number_random = Math.floor(Math.random() * (b - a + 1)) + a;
document.querySelectorAll('link[rel="stylesheet"]').forEach(link => {
  let random = _randomstring(number_random);
  link.href += '?' + random;
});

document.querySelectorAll('script').forEach(script => {
  if (script.src) {
      let random = _randomstring(number_random);
      script.src += '?' + random;
  }
});
// Resource by site
var program = "ThanhDieuTv";
var configname=config.name,Root=`\n<button onclick="__Yamate__();" id="settingstheme" class="theme-button">\n<svg class="Icon-Theme" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><g fill="currentColor" fill-opacity="0"><path d="m15.22 6.03l2.53-1.94L14.56 4L13.5 1l-1.06 3l-3.19.09l2.53 1.94l-.91 3.06l2.63-1.81l2.63 1.81z"><animate id="lineMdMoonLoop0" fill="freeze" attributeName="fill-opacity" begin="0.7s;lineMdMoonLoop0.begin+6s" dur="0.4s" values="0;1"/><animate fill="freeze" attributeName="fill-opacity" begin="lineMdMoonLoop0.begin+2.2s" dur="0.4s" values="1;0"/></path><path d="M13.61 5.25L15.25 4l-2.06-.05L12.5 2l-.69 1.95L9.75 4l1.64 1.25l-.59 1.98l1.7-1.17l1.7 1.17z"><animate fill="freeze" attributeName="fill-opacity" begin="lineMdMoonLoop0.begin+3s" dur="0.4s" values="0;1"/><animate fill="freeze" attributeName="fill-opacity" begin="lineMdMoonLoop0.begin+5.2s" dur="0.4s" values="1;0"/></path><path d="M19.61 12.25L21.25 11l-2.06-.05L18.5 9l-.69 1.95l-2.06.05l1.64 1.25l-.59 1.98l1.7-1.17l1.7 1.17z"><animate fill="freeze" attributeName="fill-opacity" begin="lineMdMoonLoop0.begin+0.4s" dur="0.4s" values="0;1"/><animate fill="freeze" attributeName="fill-opacity" begin="lineMdMoonLoop0.begin+2.8s" dur="0.4s" values="1;0"/></path><path d="m20.828 9.731l1.876-1.439l-2.366-.067L19.552 6l-.786 2.225l-2.366.067l1.876 1.439L17.601 12l1.951-1.342L21.503 12z"><animate fill="freeze" attributeName="fill-opacity" begin="lineMdMoonLoop0.begin+3.4s" dur="0.4s" values="0;1"/><animate fill="freeze" attributeName="fill-opacity" begin="lineMdMoonLoop0.begin+5.6s" dur="0.4s" values="1;0"/></path></g><path fill="none" stroke="currentColor" stroke-dasharray="56" stroke-dashoffset="56" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 6 C7 12.08 11.92 17 18 17 C18.53 17 19.05 16.96 19.56 16.89 C17.95 19.36 15.17 21 12 21 C7.03 21 3 16.97 3 12 C3 8.83 4.64 6.05 7.11 4.44 C7.04 4.95 7 5.47 7 6 Z"><animate fill="freeze" attributeName="stroke-dashoffset" dur="0.6s" values="56;0"/></path></svg>\n</button>  \n<audio style="display: none;" id="__kudasai__"><source src="//thanhdieu.com/files/ya.mp3" type="audio/mpeg"></audio>\n<header>\n  <div class="Container">\n  <div id="td_Title">\n    <h1 class="td_Color Marque" style="text-transform: uppercase;">\n    ${configname.website}\n    </h1>\n  </div>\n  <nav>\n    <ul>\n    <li class="td_Activate"><a href="${config.nav.nav1[1]}">${config.nav.nav1[0]}</a></li>\n    <li><a href="${config.nav.nav2[1]}">${config.nav.nav2[0]}</a></li>\n    <li><a href="${config.nav.nav3[1]}">${config.nav.nav3[0]}</a></li>\n    <li><a class="td_Fps"><span id="fps">60</span> FPS </a></li>\n    </ul>\n  </nav>\n  </div>\n</header>\n<div class="LoaderCover">\n<div class="Loader">\n<div class="lds-ripple">\n<div>\n </div>\n    <div>\n      </div>\n  </div>\n<br/>\n<br/>\n  <br/>\n    <br/>\n      <br/>\n        <br/>\n          <br/>\n          <br/>  \n        <br/>\n      <br/>\n    <br/>\n  <br/>\n<br/>\n<br/>\n<br/>\n<div class="loading-mobile">\n<h2 class="loading_text Loading__">\n<span class="char" style="--char-index: 1;">W</span>\n<span class="char" style="--char-index: 2;">a</span>\n<span class="char" style="--char-index: 3;">i</span>\n<span class="char" style="--char-index: 7;">t</span>\n<span class="char" style="--char-index: 8;"> </span>\n<span class="char" style="--char-index: 9;">L</span>\n<span class="char" style="--char-index: 10;">o</span>\n<span class="char" style="--char-index: 11;">a</span>\n<span class="char" style="--char-index: 12;">d</span>\n<span class="char" style="--char-index: 13;">i</span>\n<span class="char" style="--char-index: 14;">n</span>\n<span class="char" style="--char-index: 15;">g</span>\n<span class="char" style="--char-index: 16;">.</span>\n<span class="char" style="--char-index: 17;">.</span>\n<span class="char" style="--char-index: 18;">.</span>\n<span class="char" style="--char-index: 18;">🙉</span></h2>\n  </div>\n</div>\n</div>\n<div class="ThanhDieuMain">\n  <img class="Avatar" src="${configname.avatar}" width="120" height="120" alt="ThanhDieuTV">\n  <h2 class="HoVaTen__">\n  <div id="userName">${configname.hovaten}\n  <img class="TichXanh__" src="${configname.tichxanh}">\n  </div>\n </h2>\n</div>\n<h2 class="TieuSu GioiThieu"></h2>\n<div class="NgayTao__">\n<div class="GetInfo"><strong style="color: #54ff32;">Info You: </strong><a id="infoyou_">Đang Lấy Địa Chỉ...</a></div>\n<strong class="InfoYou__">Current Time:</strong>\n<u class="DateCreater__" id="today">Đang Lấy Thời Gian...</u>\n</div>\n<hr class="ThanhGachNgang">\n<div class="MangXaHoiFtThanhDieu" id="ThanhDieuFtCredits">\n  <u class="NameFtThanhDieu">- Social Network -</u><br>\n  <br>\n  <a id="Facebook" onclick="Facebook()">\n    <div class="Facebook">\n      <i class="bi bi-facebook">&nbsp; </i>Facebook (<span id="fbcount">${config.social.facebook[1]}</span>)\n    </div>\n  </a>\n  <br>\n  <a id="Youtube" onclick="Youtube()">\n    <div class="Youtube">\n      <i class="bi bi-youtube">&nbsp; </i>Youtube (<span id="ytcount">${config.social.youtube[1]}</span>)\n    </div>\n  </a>\n  <br>\n  <a id="Discord" onclick="Discord()">\n    <div class="Discord">\n      <i class="bi bi-discord">&nbsp; </i>Discord (<span id="discordcount">${config.social.discord[1]}</span>)\n    </div>\n  </a>\n  <br>\n  <a id="Instagram" onclick="Instagram()">\n    <div class="Instagram">\n      <i class="bi bi-instagram">&nbsp; </i>Instagram (<span id="igcount">${config.social.instagram[1]}</span>)\n    </div>\n  </a>\n  <hr class="ThanhGachNgang">\n </div>\n <section id="skills">\n  <div class="NameFtThanhDieu">\n    <div class="row">\n      <skills class="col-12">\n        <u class="NameFtThanhDieu">- Skill Language -<br>\n        </u>\n    </skills></div>\n  </div>\n  <div class="col-lg-6 col-12">\n    <div class="skill-box">\n      <div class="skill">\n        <div class="skill-name">HTML/CSS</div>\n        <div class="skill-bar">\n          <div class="skill-per color_html__" per="${config.skill.html_css}" style="width: ${config.skill.html_css};"></div>\n        </div>\n      </div>\n      <div class="skill">\n        <div class="skill-name">PHP</div>\n        <div class="skill-bar">\n          <div class="skill-per color_php__" per="${config.skill.php}" style="width: ${config.skill.php};"></div>\n        </div>\n      </div>\n    </div>\n  </div>\n  <div class="col-lg-6 col-12">\n    <div class="skill-box">\n      <div class="skill">\n        <div class="skill-name">JAVASCRIPT</div>\n        <div class="skill-bar">\n          <div class="skill-per color_javascript__" per="${config.skill.javascript}" style="width: ${config.skill.javascript};"></div>\n        </div>\n      </div>\n      <div class="skill">\n        <div class="skill-name">PYTHON</div>\n        <div class="skill-bar">\n          <div class="skill-per color_python__" per="${config.skill.python}" style="width: ${config.skill.python};"></div>\n        </div>\n      </div>\n    </div>\n</div>\n<hr class="ThanhGachNgang">\n<div class="XuongDong__">&nbsp;</div>\n<div class="XuongDong__">&nbsp;</div>\n<footer class="ChanTrang__">\n<p class="ThanhDieuFtCopyright">\n  <span class="copyright">Created by\n    <a href="//facebook.com/100035052735014" style="text-decoration: none;"><u style="color: aliceblue;">${configname.created_by} ♡</u></a><br>\n    <strong>All rights reserved - © ${configname.all_rights_reserved_year}</strong></span>\n</p>\n</footer>\n<div id="Toast">Resources Code V4.0.2</div>\n`,Denfender=document.getElementById("addons");Denfender.innerHTML=Root;
// Load css
(function() {
  function shuffle_elements(elements) {
    var parent = elements[0].parentNode;
    var frag = document.createDocumentFragment();
    while (elements.length) {
      frag.appendChild(elements[Math.floor(Math.random() * elements.length)]);
    }
    parent.appendChild(frag);
  }
  function ThanhDieuProtect() {
    var ThanhDieuContent = "ThanhDieuTv";
    var thanhdieutv = document.getElementById("wussun-community");
    if (thanhdieutv === null) {
      window.location = websites;
      var listobf = document.querySelectorAll("*");
      shuffle_elements(Array.from(listobf));
      return;
    } else if (thanhdieutv.innerHTML !== ThanhDieuContent){
      return;
    }
  }
  ThanhDieuProtect();
})();
(function() {
  function showToastLoop() {
    window.location = websites;
    setTimeout(showToastLoop, 3000);
  }
  var xhr = new XMLHttpRequest();
  xhr.open("GET", "./v4.0.2/css/index.css", true);
  xhr.onreadystatechange = function() {
    if (xhr.readyState === 4 && xhr.status === 200) {
      var styleTag = document.createElement("style");
      styleTag.textContent = xhr.responseText;
      document.head.appendChild(styleTag);

      if (document.querySelectorAll(".ThanhDieuDepTrai__").length === 0) {
        showToastLoop();
        styleTag.textContent = "";
      }
    }
  };
  xhr.send();
})();
// CONFIG ALERT
function __tdtv_alert(message) {
  alert(message); 

}
// CHECK CONFIG DOMAIN VERIFY
!function(){const e=[config.name.website],t=window.location.hostname;if(!e.includes(t)){$.ajax({url:"./install/delete.php",type:"POST",success:function(e){},error:function(e){console.error("Lỗi: ",e)}});const e=document.createElement("div");e.innerText="Config name site thất bại, hệ thống đã tự xóa cấu hình, vui lòng config lại theo yêu cầu.!",e.style.backgroundColor="black",e.style.color="white",e.style.padding="15px",e.style.textAlign="center",e.style.position="fixed",e.style.opacity="0.9",e.style.top="50%",e.style.left="50%",e.style.transform="translate(-50%, -50%)",e.style.width="100%",e.style.height="100vh",e.style.display="flex",e.style.justifyContent="center",e.style.alignItems="center",e.style.fontSize="20px",e.style.zIndex="9999",document.body.appendChild(e)}}();
// CHECK CONFIG NAME OWNER
var owner = [configname.hovaten];
var websites = ["https://" + config.name.website];
var ownerfound = false;
var domainowner = config.name.website;
domainowner = domainowner.toLowerCase();
for (var i = 0; i < owner.length; i++) {
    var ownername = owner[i];
    var name_elements = document.querySelectorAll(".HoVaTen__");
    for (var j = 0; j < name_elements.length; j++) {
        var usernamediv = name_elements[j].querySelector("#userName");
        if (usernamediv) {
            var userName = usernamediv.textContent.trim();
            
            if (userName === ownername) {
                ownerfound = true;
                break; 
            }
        }
    }
    if (!ownerfound) {
      __tdtv_alert("Code has been bugged @"+ program);
        window.location = websites;
    }
    
    ownerfound = false;
}
var title_element = document.getElementById("td_Title");
if (title_element) {
    var titleText = title_element.textContent.trim();
    if (titleText === domainowner) {
    } else {
      __tdtv_alert("Code has been bugged 0x000002 @"+ program);
        window.location = websites;
    }
}
// NHÚNG THƯ VIỆN JS
var script = document.createElement("script");
script.src = "//cdn.jsdelivr.net/npm/sweetalert2@11";
//script.onload = function() {ThanhDieuDieuAudio();};
document.head.appendChild(script);
var style = document.createElement("style");
style.innerHTML="\n.rainbow {\n  animation: rainbow 1s linear infinite;\n}\n\n\n.blink-title {\n  animation: blink 1s infinite;\n}\n\n@keyframes blink {\n  50% {\n    opacity: 0;\n  }\n}\n",document.head.appendChild(style);var x=document.getElementById("__kudasai__"),playcount=0;x.addEventListener("ended",(function(){++playcount<1&&(x.currentTime=0,x.play())}));
// ANIME CATBOX
var div=document.createElement("div");div.className="thanhdieu-elements";var img=document.createElement("img");img.className="anime-cb",img.src="//api.thanhdieu.com/anime-cb.php",div.appendChild(img),document.body.appendChild(div);
// YAMATE KUDASAI ~
function __Yamate__() {
  playcount = 0;
  x.currentTime = 0; 
  x.play();
}
/*function HomeThanhDieu() {
  Swal.fire({
    title: '<span class="blink-title">𝙉𝙤𝙩𝙞𝙛𝙞𝙘𝙖𝙩𝙞𝙤𝙣 !</span>',
    html: '<span class="rainbow-text">=>>🐬Nice !! You Gay =))🐬<<=</span>',
    showConfirmButton: false,
    onBeforeOpen: () => {
      const content = Swal.getContent();
      const element = content.querySelector('.rainbow-text');
      const text = element.innerText;
      const letters = text.split('');
      element.innerHTML = '';
  
      letters.forEach((letter, i) => {
        const span = document.createElement('span');
        span.textContent = letter;
        span.style.animationDelay = `${i * 50}ms`;
        span.classList.add('rainbow');
        element.appendChild(span);
      });
    }
  });
}*/
// SKILL LANGUAGE
$(".skill-per").each((function(){var t=$(this),a=t.attr("per");t.css("width",a+"%"),$({animatedValue:0}).animate({animatedValue:a},{duration:1e4,step:function(){t.attr("per",Math.floor(this.animatedValue)+"%")},complete:function(){t.attr("per",Math.floor(this.animatedValue)+"%")}})}));
  
function __StartFontEnd() {
ShowToast(); 
HomeThanhDieu();
CheckFps();
ThanhDieuAudio();
}
// Add music
const ThanhDieu_List = config.music.linkbaihat;
//Random music
let index=Math.floor(Math.random()*ThanhDieu_List.length);const audio=new Audio(ThanhDieu_List[index]);function ThanhDieuAudio(){audio.play()}document.addEventListener("click",ThanhDieuAudio),audio.addEventListener("ended",(function(){index=(index+1)%ThanhDieu_List.length,audio.src=ThanhDieu_List[index],audio.play()}));
function HomeThanhDieu() {
const windowWidth = window.innerWidth;
let width = '28%';

if (windowWidth < 768) {
  width = '90%';
}
Swal.fire({html:`<h2 class="titleswal">&mdash; ${config.thongbao.title} &mdash;</h2><p class="alertswal">${config.thongbao.noidung}</p>`,showClass:{popup:"animate__animated animate__fadeInDown"},hideClass:{popup:"animate__animated animate__fadeOutUp"},showConfirmButton:!1,width:width});
}
function ThemeChangeDone() {
/*
@ ThanhDieuVersion 4.0.0
*/
}
// CHECK FPS/S
var fps = document.getElementById("fps");
var startTime = Date.now();
var frame = 0;
function CheckFps() {
var time = Date.now();
frame++;
if (time - startTime > 1000) {
fps.innerHTML = (frame / ((time - startTime) / 1000)).toFixed(1);
startTime = time;
frame = 0;
}
window.requestAnimationFrame(CheckFps);
}
// BIO TYPE TEXT
let typed = new Typed(".GioiThieu", {
strings: [config.name.tieusu[0], config.name.tieusu[1],  config.name.tieusu[2]],
typeSpeed: 95,
backSpeed: 75,
loop: true
})
// SOCIAL NETWORK
function LinkSocial(o){setTimeout((function(){window.open(o,"_blank").focus()}),1900),toastr.success("Thank you for caring me :>","Notification !")}function Facebook(){LinkSocial(config.social.facebook[0])}function Youtube(){LinkSocial(config.social.youtube[0])}function Discord(){LinkSocial(config.social.discord[0])}function Instagram(){LinkSocial(config.social.instagram[0])}
  // TOAST SHOW
  function ShowToast() {
	var x = document.getElementById("Toast");
	x.className = "show";
	setTimeout(function(){ x.className = x.className.replace("show", ""); }, 5000);
  }
  // LOADER SCREEN
  let spin = document.querySelector('.LoaderCover');
window.addEventListener('load', () => {
  spin.classList.add('HideLoader');
  setTimeout(() => {
    spin.remove();
  }, 2200);
});
setTimeout(() => {
  // VIEW TIME CURRENT
function updateTime() {
  const now = new Date();
  const options = {
    hour: 'numeric',
    minute: 'numeric',
    second: 'numeric',
    hour12: false,
    timeZone: 'Asia/Ho_Chi_Minh'
  };

  const timeString = now.toLocaleString('en-US', options);
  const hours = now.getHours();

  let timeOfDay = '';
  if (hours >= 5 && hours < 12) {
    timeOfDay = 'Buổi Sáng 🌞';
  } else if (hours >= 12 && hours < 18) {
    timeOfDay = 'Buổi Chiều 🌒';
  } else {
    timeOfDay = 'Buổi Tối 🌚';
  }

  document.getElementById('today').textContent = `${timeString} ${timeOfDay}`;
}

setInterval(updateTime, 1000);
}, 3000);
// VIEW IP/COUNTRY BY API
var all_down_sum = 0;
var run = false;
var checkIP = false;
var visibl = true;
var thread_down = [];
var lsat_all_down = 0;
var refresh_lay = 5e3;
async function start_thread(index) {
    try {
        const response = await fetch(testurl, {
            cache: "no-store",
            mode: "cors",
            referrerPolicy: "no-referrer"
        });
        const reader = response.body.getReader();
        while (true) {
            const {
                value,
                done
            } = await reader.read();
            if (done) {
                reader.cancel();
                start_thread(index);
                break;
            }
            if (!run) {
                reader.cancel();
                break;
            }
            thread_down[index] += value.length;
        }
    } catch (err) {
        console.log(err);
        if (run) start_thread(index);
    }
}
var gbip = ""; 
var geoip = "https://api.ip.sb/geoip"; // CHANGE API GET IP
function clear_console() {
  console.clear();
}
function checkip_address(){visibl&&fetch(geoip,{referrerPolicy:"no-referrer"}).then((e=>e.json())).then((e=>{var n=document.getElementById("infoyou_");n.innerText=e.ip+" "+e.isp,e.ip!==gbip&&(n.style.color="",e.ip,clear_console()),gbip=e.ip})).catch((e=>{document.getElementById("infoyou_").innerText="GEO IP FAILED !",clear_console()})),setTimeout(checkip_address,6e3)}function noisong(){visibl&&fetch(geoip,{referrerPolicy:"no-referrer"}).then((e=>e.json())).then((e=>{var n=document.getElementById("infoyou_");n.innerText=e.region+" "+e.country,e.ip!==gbip&&(n.style.color="",e.ip,clear_console()),gbip=e.ip})).catch((e=>{document.getElementById("infoyou_").innerText="ERROR RELOAD LIMIT!",clear_console()})),setTimeout(noisong,5e3)}setInterval(checkip_address,3e3),noisong();
function ___hexenc() {
  var randomnumber = Math.floor(Math.random() * 100000);
  var hex = '0x' + randomnumber.toString(16);
  return hex;
}

function ___numberenc() {
  return Math.floor(Math.random() * 10).toString();
}

function ___strenc(length) {
  const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
  let __obf = '';
  for (let i = 0; i < length; i++) {
    const randomIndex = Math.floor(Math.random() * characters.length);
    __obf += 'x' + characters.charAt(randomIndex) + '/';
  }
  return __obf.slice(0, -1);
}

/*
@ Made by Wussun Community ♡;
@ Copyright all rights reserved 2019-2023
- With from owner ThanhDieuㅤूाीू
*/