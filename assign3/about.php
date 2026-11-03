<?php
// Description: Aboout page for site
// Author: Charlie Hutcheson
// Date: 2026-11-03
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Discover the very best in flash storage at Sandish">
    <meta name="keywords" content="Sandish, Flash, Storage, USB">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="styles/style-timetable.css">
    <link rel="icon" type="image/x-icon" href="images/favicon.png">
    <title>About Us</title>
</head>

<body>
    <?php include_once("components/navbar.inc.php") ?>

    <main>
        <br>
        <h1>About Us</h1>
        <br>
        <p>
            If you are not satisfied with any aspect of our products, please contact us.
        </p>
        <br>
        <!-- START Profile Card -->
        <div class="center-container">
            <dl class="definition">
                <dt><span class="icons-person">Name</span></dt>
                <dd>Charlie Hutcheson</dd>
                <dt><span class="icons-id">Student number</span></dt>
                <dd>106498627</dd>
                <dt><span class="icons-school">Tutor name</span></dt>
                <dd>Bo Li</dd>
                <dt><span class="icons-book">Course</span></dt>
                <dd>Graduate Certificate of Data Science</dd>
                <dt><span class="icons-email">Email</span></dt>
                <dd><a href="mailto:106498627@student.swin.edu.au">106498627@student.swin.edu.au</a></dd>
                <dt><span class="icons-photo">Photo</span></dt>
                <!-- for privacy reasons i refuse to provide a photo of myself, enjoy this meme instead. -->
                <dd>
                    <figure>
                        <img class="photo-border" src="images/profile.jpg " alt="">
                        <figcaption>Not a photo of me, but a good GoT meme nonetheless.</figcaption>
                    </figure>
                </dd>
            </dl>
        </div>
        <!-- END Profile Card -->
        <br>
        <!-- START Timetable -->
        <h2>Timetable</h2>
        <br>
        <!-- scrolls sideways on phones instead of widening the page -->
        <div class="table-scroll">
            <table class="timetable">
                <thead>
                    <tr>
                        <th scope="col">Time</th>
                        <th scope="col">Mon</th>
                        <th scope="col">Tue</th>
                        <th scope="col">Wed</th>
                        <th scope="col">Thu</th>
                        <th scope="col">Fri</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row">8:00</th>
                        <td class="work" rowspan="17">Work</td>
                        <td class="work" rowspan="17">Work</td>
                        <td class="work" rowspan="9">Work</td>
                        <td class="work" rowspan="9">Work</td>
                        <td class="work" rowspan="17">Work</td>
                    </tr>
                    <tr>
                        <th scope="row">8:30</th>
                    </tr>
                    <tr>
                        <th scope="row">9:00</th>
                    </tr>
                    <tr>
                        <th scope="row">9:30</th>
                    </tr>
                    <tr>
                        <th scope="row">10:00</th>
                    </tr>
                    <tr>
                        <th scope="row">10:30</th>
                    </tr>
                    <tr>
                        <th scope="row">11:00</th>
                    </tr>
                    <tr>
                        <th scope="row">11:30</th>
                    </tr>
                    <tr>
                        <th scope="row">12:00</th>
                    </tr>
                    <tr>
                        <th scope="row">12:30</th>
                        <td class="data" rowspan="4">Big Data Lecture</td>
                        <td class="data" rowspan="2">Big Data Tutorial</td>
                    </tr>
                    <tr>
                        <th scope="row">13:00</th>
                    </tr>
                    <tr>
                        <th scope="row">13:30</th>

                        <td class="work" rowspan="8">Work</td>
                    </tr>
                    <tr>
                        <th scope="row">14:00</th>
                    </tr>
                    <tr>
                        <th scope="row">14:30</th>
                        <td class="work" rowspan="8">Work</td>
                    </tr>
                    <tr>
                        <th scope="row">15:00</th>
                    </tr>
                    <tr>
                        <th scope="row">15:30</th>
                    </tr>
                    <tr>
                        <th scope="row">16:00</th>
                    </tr>
                    <tr>
                        <th scope="row">16:30</th>
                        <td class="webapp" rowspan="4">Creating Web Applications Tutorial</td>
                        <td rowspan="10"></td>
                        <td rowspan="10"></td>
                    </tr>
                    <tr>
                        <th scope="row">17:00</th>
                    </tr>
                    <tr>
                        <th scope="row">17:30</th>
                        <td rowspan="8"></td>
                    </tr>
                    <tr>
                        <th scope="row">18:00</th>
                    </tr>
                    <tr>
                        <th scope="row">18:30</th>
                        <td class="webapp" rowspan="4">Creating Web Applications Lecture</td>
                        <td rowspan="6"></td>
                    </tr>
                    <tr>
                        <th scope="row">19:00</th>
                    </tr>
                    <tr>
                        <th scope="row">19:30</th>
                    </tr>
                    <tr>
                        <th scope="row">20:00</th>
                    </tr>
                    <tr>
                        <th scope="row">20:30</th>
                        <td rowspan="2"></td>
                    </tr>
                    <tr>
                        <th scope="row">21:00</th>
                    </tr>
                </tbody>
            </table>
        </div>
        <br>
        <!-- END Timetable -->
    </main>

    <?php include_once("components/footer.inc.php") ?>
</body>

</html>