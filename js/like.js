function addLike($url,$id,$isOn){
    //pridej like
    fetch($url,{
        credentials: "same-origin"
    });
    //updatuj ty pocty liku
    fetch('like.php?likecount&id='+$id)
        .then(response => response.text())
        .then(body => {for(let a of document.querySelectorAll('[id=l'+$id+']')){
            a.innerHTML=body
        }}
    )
    //oprav barvy
    for(let a of document.querySelectorAll('[id=lc'+$id+']')){
        if(a.classList.contains('liked') || !$isOn)
            a.classList.remove('liked');
        else
            a.classList.add('liked');
    }

}

function addRepost(id,loggedOn){
    if(loggedOn==0)
        return
    //updatnuj
    let url="quote.php?tweet="+id;
    fetch(url,{
        credentials: "same-origin"
    });

    //updatuj ty pocty repostu
    fetch(url+"&getCount")
        .then(response => response.text())
        .then(body => {for(let a of document.querySelectorAll('[id=r'+id+']')){
            a.innerHTML=body
        }}
    )

    //oprav barvy
    for(let a of document.querySelectorAll('[id=rc'+id+']')){
        if(a.classList.contains('green'))
            a.classList.remove('green');
        else
            a.classList.add('green');
    }
}