

var movieKey = ["id","title","originalTitle","director","genre","year","runtime","posterUrl","trailerUrl","synopsis","cast","rating","comment","listed"];
var boxIsBottom = false

$(function() {

    Movie.getMovies('all')

    Form.init()
    
    $('.genre').click(function(){
        displayMenu("movie-collection")
        Movie.getMovies($(this).attr("data")||$(this).html())
        $('#main-title').html($(this).html())
    })
    
     $(window).scroll(function() {
        if ($(this).scrollTop() + $(this).innerHeight() + 200 >= $("html")[0].scrollHeight && boxIsBottom == false) {
            
            boxIsBottom = true
            Movie.getMovies(false,()=>{ boxIsBottom = false })
        }
    });

    document.getElementById("search-form").addEventListener("submit", function(event){
        event.preventDefault()
        let search = event.target[0].value
        if(search.length){
            Movie.seach(search)
            displayMenu("movie-collection")
            $('#main-title').html("Search")
        }
    });
});

/******************* menu */

function displayMenu(menu) {
    $('section').hide()
    $("body").animate({ scrollTop: 0 },0);
    $('#'+menu).show()
    boxIsBottom = false;
}

/******************* toast */

function toast(text, time) {
    $('#toast').html(text).animate({'top': '60px', 'display': 'block'},500)
    setTimeout(()=>{
        $('#toast').animate({'top': '-60px', 'display': 'none'},500).html('')
    }, time*1000)
}