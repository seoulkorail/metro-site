var replaceAt = function(input, index, character){
    return input.substr(0, index) + character + input.substr(index+character.length);
}

timerId = ""
timerId1 = ""

function selectLine(line){
    showTrain(line)
}

async function showTrain(line){
    clearInterval(timerId);
    clearInterval(timerId1);
    ia = 0;
    fetch(`./getStn.php?line=`+line)
    .then((response) => response.json())
    .then((data) => {
        document.querySelector("#viewTrain").innerHTML = "";
        data.forEach(statnNm => {
            station = document.createElement("div");
            station.setAttribute('class', 'station');
    
            //자식
            leftdiv = document.createElement("div");
            leftdiv.setAttribute('class', 'leftdiv');
            leftdiv.setAttribute('id', statnNm+"하");
    
            stname = document.createElement("div");
            stname.setAttribute('class', 'stname');
            stname.innerHTML = '<p>' + statnNm + '</p>';
    
            rightdiv = document.createElement("div");
            rightdiv.setAttribute('class', 'rightdiv');
            rightdiv.setAttribute('id', statnNm+"상");
    
            station.appendChild(leftdiv);
            station.appendChild(stname);
            station.appendChild(rightdiv);
    
            document.querySelector("#viewTrain").appendChild(station);
        });

        timercd = 1;

          getTrain(line);
          
          
          timerId = setInterval(() => {
            getTrain(line);
          }, 15000);

          
          timerId1 = setInterval(() =>{
            timercd++;
            info=""
            if(timercd == 1){
                dv = document.getElementsByClassName("d1");
                for (var i=0;i<dv.length;i+=1){
                    dv[i].style.display = 'block';
                }
                dv = document.getElementsByClassName("d2");
                for (var i=0;i<dv.length;i+=1){
                    dv[i].style.display = 'none';
                }
            }
            if(timercd == 2){
                dv = document.getElementsByClassName("d1");
                for (var i=0;i<dv.length;i+=1){
                    dv[i].style.display = 'none';
                }
                dv = document.getElementsByClassName("d2");
                for (var i=0;i<dv.length;i+=1){
                    dv[i].style.display = 'block';
                }
            }
            if(timercd == 2)
                timercd = 0;
        }, 3000);
    });
    
}

async function getTrain(line){
  let result =await axios.get(`./getData.php?line=` + line, {
  });
  data = result.data;
  document.querySelectorAll(".leftdiv").forEach((v)=>v.innerHTML = "");
  document.querySelectorAll(".rightdiv").forEach((v)=>v.innerHTML = "");
  
  data.forEach(trainList=> {
      if(trainList == null) return;
      makeTrainList(trainList, line);
  });
}

function makeTrainList(data, line) {
    if (data.dir == "상행") {
      updn = '상';
    } else {
        
        updn = '하';
    }
    if(data.statnTnm == null) data.statnTnm = "?"
    tp = data.trainP != "" ? data.trainP : "?";

    tpinfo = `<div class="trainInfo2" style="left:7.5px;"><p class="wrin1" style=" font-size: 9pt;">` + tp + `</p></div>`;
    if(data.trainP.length == 6)
    tpinfo = `<div class="trainInfo2" style="left:-0.5px"><p class="wrin1" style=" font-size: 6pt;">` + tp + `</p></div>`;

    tjfaud = '<div class="trnio d1"><p class="wrin1">' + data.trainNo + '</p></div>'+'<div class="trnio d2" style="display:none"><p class="wrin1">' + data.statnTnm + '</p></div>'+tpinfo;

    trainP = data.trainP;
    
    let stan = document.getElementById(data.statnNm + updn);
    if(stan == null){
      return;
    }
    teleport = data.trainNo
    if(line == "5" || line == "6" || line == "7" || line == "8")
        teleport = "SMRT"+data.trainNo.substr(-4)
    
    if(data.directAt == 0)
        stan.innerHTML = stan.innerHTML + '<div ondblclick="window.open(`https://rail.blue/railroad/logis/Default.aspx?train='+teleport+'`)" class="traina train'+data.sts+updn+' '+data.trainNo+" "+trainP+'"><div>' + tjfaud + '<div class="trainInfo3"><p>' + " "+ data.sts + '</p></div></div></div>&nbsp;';
    if(data.directAt == 1)
        stan.innerHTML = stan.innerHTML + '<div ondblclick="window.open("https://rail.blue/railroad/logis/Default.aspx?train='+teleport+'`)" class="traina train'+data.sts+updn+' '+data.trainNo+" "+trainP+'"><div>' + tjfaud + '<div class="trainInfo3"><p class="dir">' +  " " + data.sts + '</p></div></div></div>&nbsp;';
}