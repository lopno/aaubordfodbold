<?php
include "functions/html.php";
printHeader("AAU Bordfodbold - Frequently Asked Questions", "Frequently Asked Questions");
?>

    <h3>What is this?</h3>
    <p>AAU bordfodbold is a rating system where people can submit table football games played at Aalborg University in order to get ranked.</p>
    <h3>How does it work?</h3>
    <p>The rating system is based on the <a href="http://en.wikipedia.org/wiki/Elo_rating_system">Elo rating system</a>.<br />
    AAU bordfodbold uses a constant K-factor of 20.
    This means that if player A defeats player B of equal rating, player A will gain 10 rating points and player B will lose 10 rating points.<br />
    How many points that are won and lost are determined by the players' rating.
    If player A has a high rating and defeats player B who has a low rating, player A will gain less than 10 points and player B will lose less than 10 points.
    If player B has a low rating and defeats player A who has a high rating, player B will gain more than 10 points and player A will lose more than 10 points.
    New players have a rating of 1500.
    Players have a individual rating and a rating for every player he has ever played with.
    This means that if a player plays with a new player they will have a team rating of 1500.
    The individual rating is only used when games are played one versus one.
    </p>
    <h3>Why isn't there a combined rating for individual and team matches?</h3>
    <p>There is! It can be seen on the <a href="profile.php">Player Profile</a> page</p>
    
    <h3>I have found a bug in the system, what do I do?</h3>
    <p>Send an email to <A HREF="mailto:mikaelmidt@gmail.com">mikaelmidt@gmail.com</A> and explain the bug.</p>
    
    
<?php
printFooter();
?>