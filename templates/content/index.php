
<div id="main-title" class="header">Movie collection</div>

<section id="movie-collection"></section>
<section id="movie-preview"></section>

<section id="movie-form">
    <form id="search-imdb" action="">
        <label>Search movie to automatically complete fields</label>
        <input type="text" id="searchImdbInput" name="searchImdbInput" placeholder="Title & Year... or Imdb id: tt0317919...">
        <input type="submit" value="Search & Conplete">
        <div id="loading" class="icon-loading"></div>
    </form>
    <hr style="margin: 30px 10px;">
    <form id="movie-insert-form" action="">
        <div class="col-6">
            <input type="text" id="id" hidden>
            <label>Title</label>
            <input type="text" id="title" require>
            <label>Original title</label>
            <input type="text" id="originalTitle">
            <label>Imdb Id</label>
            <input type="text" id="imdbId">
            <label>Director</label>
            <input type="text" id="director">
            <label>Genre</label>
            <input type="text" id="genre">
            <label>Year</label>
            <input type="text" id="year">
            <label>Runtime</label>
            <input type="text" id="runtime">
        </div>
        <div class="col" style="margin: 10px 50px;">
          <div id="posterUrlFormPreview-box">
            <img id="posterUrlFormPreview" src="">
          </div>
        </div>
        <div class="col-12">
            <label>Cast</label>
            <input type="text" id="cast">
            <label>Poster Url</label>
            <input type="text" id="posterUrl">
            <label>Trailer Url</label>
            <input type="text" id="trailerUrl">
            <label>synopsis</label>
            <textarea id="synopsis" cols="30" rows="10"></textarea>
            <div style="margin:15px 0;">
                <input type="checkbox" id="listed" class="checkbox">
                <label for="listed">Add to my list</label>
            </div>
            <label>My rating</label>
            <input type="range" id="rating" min="0" max="100">
            <label>My comment</label>
            <textarea id="comment" cols="30" rows="10"></textarea>
        </div>
        <input type="submit" id="add" value="">
    </form>
</section>

<div id="toast">sqdqsd</div>