function addLike($url,$id,$isOn){
    //pridej like
    fetch($url,{
        credentials: "same-origin"
    });
    //updatuj ty pocty liku
    fetch('like.php?likecount&id='+$id)
        .then(response => response.text())
        .then(body => document.getElementById('l'+$id).innerHTML=body)
    //oprav barvy
    if(document.getElementById('lc'+$id).classList.contains('liked') || !$isOn)
        document.getElementById('lc'+$id).classList.remove('liked');
    else
        document.getElementById('lc'+$id).classList.add('liked');

    
}