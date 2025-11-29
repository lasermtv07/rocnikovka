function validateNick(){
    let regex=/^[A-Za-z0-9ÁČĎÉĚÍŇÓŘŠŤÚŮÝŽáčďéěíňóřšťúůýž -]+$/g;
    let str=document.getElementById("nick").value;
    if(!regex.test(str) || str.length<3){
        document.getElementById("validNick").innerHTML="<br>Illegal username!";
        document.getElementById("labelNick").innerHTML="<br>&nbsp";
    }
    else {
        document.getElementById("validNick").innerHTML="";
        document.getElementById("labelNick").innerHTML="";
    }
}

function validatePass(){
    let str=document.getElementById("pass").value;
    if(str.length<5){
        document.getElementById("validPass").innerHTML="<br>Illegal password!";
        document.getElementById("labelPass").innerHTML="<br>&nbsp";
    }
    else {
        document.getElementById("validPass").innerHTML="";
        document.getElementById("labelPass").innerHTML="";
    }
}

function validateConf(){
    let str=document.getElementById("pass").value;
    let conf=document.getElementById("conf").value;
    if(str!=conf || conf==""){
        document.getElementById("validConf").innerHTML="<br>Passwords don't match!";
        document.getElementById("labelConf").innerHTML="<br>&nbsp";
    }
    else {
        document.getElementById("validConf").innerHTML="";
        document.getElementById("labelConf").innerHTML="";
    }
}

function validateMail(){
    let regex=/[a-zA-Z0-9]+\@[a-zA-Z0-9]+\.[a-zA-Z0-9]+/g;
    let str=document.getElementById("email").value;
    if(!regex.test(str)){
        document.getElementById("validEmail").innerHTML="<br>Wrong email format!";
        document.getElementById("labelEmail").innerHTML="<br>&nbsp";
    }
    else {
        document.getElementById("validEmail").innerHTML="";
        document.getElementById("labelEMail").innerHTML="";
    }
}

function validateGender(){
    let str=document.getElementById("gender").value;
    if(str==""){
        document.getElementById("validGender").innerHTML="<br>Select a gender!";
        document.getElementById("labelGender").innerHTML="<br>&nbsp";
    }
    else {
        document.getElementById("validGender").innerHTML="";
        document.getElementById("labelGender").innerHTML="";
    }
}

validateNick();
validatePass();
validateConf();
validateMail();
validateGender();

document.getElementById("nick").onchange=()=>{validateNick()};
document.getElementById("pass").onchange=()=>{validatePass();validateConf();};
document.getElementById("conf").onchange=()=>{validateConf();};
document.getElementById("email").onchange=()=>{validateMail()};
document.getElementById("gender").onchange=()=>{validateGender()};