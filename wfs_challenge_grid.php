<!-- -------------------- STYLES -------------------- -->
<style type="text/css">
.wfs-challenge-grid {
    width: 370px;
    margin: auto;
    /*display: flex !important;
    flex-wrap: wrap !important;
    justify-content: center !important;*/
 }
 
.wfs-challenge-grid > a {
    color: #000;
    cursor: pointer;
 }

@media only screen and (min-width: 900px) {
    .wfs-challenge-grid {
        width: 760px !important;
    }
} 

@media only screen and (min-width: 1200px) {
    .wfs-challenge-grid {
        width: 1080px !important;
    }
} 
 
@media only screen and (min-width: 1600px) {
    .wfs-challenge-grid {
        width: 1450px !important;
    }
} 
 
 
.wfs-challenge-grid-item {
/*position: static !important;*/
flex-direction: row !important;
 /*float: left;*/
 width: 320px; 
 height: 440px;
 overflow: hidden;
 box-shadow: rgba(0, 0, 0, 0.298039) 0px 12px 18px -6px;
 background: #fff;
 margin: 20px;
 }
 
 .wfs-challenge-title {
 font-family: 'Amaranth', sans-serfi;
 color: #5CB74D;
 font-size: 20px;
 overflow: hidden;
 margin-top: 15px;
 margin-bottom: 0px;
 margin-left: 20px;
 margin-right: 20px;
 padding: 0px;
 padding-bottom: 2px;
 }
 
 .wfs-challenge-meta {
 margin-top: 5px;
 margin-bottom: 0px;
 margin-left: 20px;
 margin-right: 20px;
 padding: 0px;
 font-style: italic;
 }
 
 .wfs-challenge-text {
 margin-top: 5px;
 margin-bottom: 0px;
 margin-left: 20px;
 margin-right: 20px;
 padding: 0px;
 text-align: justify;
 line-height: 21px;
 }
</style>



<!-- -------------------- GRID HTML -------------------- -->
<div class="wfs-challenge-grid">
    
    
<!-- -------------------- PHP SUCHE -------------------- -->
<?php 
    // SUCHKRITERIEN bestimmen
    $suchkriterien = array(
        'post_type' => 'challenge',
        'posts_per_page' => -1, //show all posts
        'orderby' => 'title',
        'order' => 'ASC'
        //'meta_key' => 'wpcf-punktzahl',
        //'meta_value' => '8',
        //'meta_compare' => '='
    );
    
    // Suche ausführen
    $suche = new WP_Query($suchkriterien);
    while($suche->have_posts()) : $suche->the_post();
    $attribute = get_post_custom();
?>    
 <a onclick="wfsOpenModal('https://weltfairsteher.de/challenges/modal/?challengeID=<?php echo(get_the_ID()); ?>')"><div class="wfs-challenge-grid-item <?php foreach((get_the_category()) as $category){echo $category->name." ";}?>">
 <?php the_post_thumbnail(); ?>
 <h3 class="wfs-sort-name wfs-challenge-title"><?php the_title(); ?></h3>
 <p class="wfs-challenge-meta">
 <span class="wfs-sort-kategorie"><?php foreach((get_the_category()) as $category){echo $category->name." ";}?></span><br />
 <span class="wfs-sort-punkte"><?php echo $attribute['wpcf-punktzahl'][0]; ?></span> Punkte
 </p>
 <p class="wfs-challenge-text"><?php echo(get_the_excerpt()); ?></p>
 </div></a>

 <?php endwhile; wp_reset_query(); // Suche zurücksetzen ?>
 </div>
 
 

<!-- -------------------- ISOTOPE JS -------------------- -->
<script src='//cdnjs.cloudflare.com/ajax/libs/jquery/2.2.2/jquery.min.js'></script>
<script src='//npmcdn.com/isotope-layout@3/dist/isotope.pkgd.js'></script>

<script>
 // init Isotope
 var $grid = $('.wfs-challenge-grid').isotope({
 itemSelector: '.wfs-challenge-grid-item',
 layoutMode: 'fitRows',
 getSortData: {
 kategorie: ".wfs-sort-kategorie",
 punkte: '.wfs-sort-punkte parseInt',
 name: ".wfs-sort-name"
 },
 sortBy: 'name',
 sortAscending: {
 name: true,
 punkte: false
 }
 });

 // bind sort button click
// $('.wfs-sort-menu').on('click', 'button', function() {
$('.wfs-sort-menu').on('click', 'li', function() {
 var sortValue = $(this).attr('data-sort-value');
 $grid.isotope({
 sortBy: sortValue
 });
 });
 
 // bind filter button click
//$('.wfs-filter-menu').on( 'click', 'button', function() {
$('.wfs-filter-menu').on( 'click', 'li', function() {
    var filterValue = $(this).attr('data-filter');
    // use filter function if value matches
    $grid.isotope({ filter: filterValue });
});

 // change is-checked class on buttons
 $('.wfs-dropdown-menu').each(function(i, buttonGroup) {
 var $buttonGroup = $(buttonGroup);
 $buttonGroup.on('click', 'li', function() {
    $buttonGroup.find('.wfs-dropdown-item-active').removeClass('wfs-dropdown-item-active');
    $(this).addClass('wfs-dropdown-item-active');
 });
 });

 </script>