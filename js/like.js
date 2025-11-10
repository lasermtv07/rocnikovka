function addLike($url,$id,$isOn){
    //pridej like
    fetch($url,{
        credentials: "same-origin"
    });
    //updatuj ty pocty liku
    /*fetch('like.php?likecount&id='+$id)
        .then(response => response.text())
        .then(body => {for(let a of document.querySelectorAll('[id=l'+$id+']')){
            a.innerHTML=body
        }}
    )*/
    for(let a of document.querySelectorAll('[id=l'+$id+']')){
        let original=a.innerHTML;
        for(let b of document.querySelectorAll('[id=lc'+$id+']')){
            if(a.innerHTML==original && $isOn){
                if(b.classList.contains('liked'))
                    a.innerHTML=parseInt(a.innerHTML)-1;
                else
                    a.innerHTML=parseInt(a.innerHTML)+1;
            }
        }
    }

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
    /*
    fetch(url+"&getCount")
        .then(response => response.text())
        .then(body => {for(let a of document.querySelectorAll('[id=r'+id+']')){
            a.innerHTML=body
        }}
    )*/
    for(let a of document.querySelectorAll('[id=r'+id+']')){
        let original=a.innerHTML;
        for(let b of document.querySelectorAll('[id=rc'+id+']')){
            if(a.innerHTML==original){
                if(b.classList.contains('green') || !loggedOn)
                    a.innerHTML=parseInt(a.innerHTML)-1;
                else
                    a.innerHTML=parseInt(a.innerHTML)+1;
            }
        }
    }

    //oprav barvy
    for(let a of document.querySelectorAll('[id=rc'+id+']')){
        if(a.classList.contains('green'))
            a.classList.remove('green');
        else
            a.classList.add('green');
    }
}