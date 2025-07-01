<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="../CSS/index.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../CSS/hidden.js" defer></script> 
    <script src="../View/categories.js"></script>
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap');
    </style>
</head>

<body>
    <nav>
        <div class="nav-logo">
            <img src="../imgs/logo.png" alt="logo.png">
        </div>
        <div class="nav-cont">
            <li class="nav-list">
                <ol><button><a href="../View/index.php">Home</a></button></ol>
                <div id="category-dropdown" class="dropdown">
            <ol>
                <button class="dropdown-btn">Category</button>
            </ol>
            <div class="dropdown-content">
                <div class="categories-container">
                    <div class = lined>
                        <p>Browse services across various categories to find what you need.</p>
                        <img src="../imgs/close.png" alt="exit.png">
                    </div>
                <div class="category-row">
                    <div class="category-item">Accounting & Bookkeeping</div>
                    <div class="category-item">Art & Illustration</div>
                    <div class="category-item">Cleaning Services</div>
                    <div class="category-item">Consulting</div>
                    <div class="category-item">Customer Service</div>
                    <div class="category-item">Delivery Services</div>
                </div>
                <div class="category-row">
                    <div class="category-item">Digital Marketing</div>
                    <div class="category-item">Fashion & Styling</div>
                    <div class="category-item">Fitness & Wellness</div>
                    <div class="category-item">Food & Beverages</div>
                    <div class="category-item">Graphic Design</div>
                    <div class="category-item">Handyman Services</div>
                </div>
                <div class="category-row">
                    <div class="category-item">Landscaping and Gardening</div>
                    <div class="category-item">Music & Audio</div>
                    <div class="category-item">Online Tutoring</div>
                    <div class="category-item">Pet care</div>
                    <div class="category-item">Photography & Videography</div>
                    <div class="category-item">Tech Support</div>
                </div>
                <div class="category-row">
                    <div class="category-item">Translation Services</div>
                    <div class="category-item">Transportation</div>
                    <div class="category-item">Virtual Assistance</div>
                    <div class="category-item">Web Development</div>
                    <div class="category-item">Writing & Editing</div>
                    <div class="category-item">Others</div>
                </div>
                </div>
            </div>
            </div>
                <ol><button>Find Talent</button></ol>
                <ol><form action="login.php">
                        <button type="submit" class="log-in">Log in</button>
                    </form>
                </ol>
                <ol><button class="sign-up">Join us</button></ol>
            </li>
    </nav>
<section class = "container">
<div class="container">
    <div class = left>
    <div class="header">
        <h1>Hustle quick and watch opportunities <span class="stick">stick</span> !</h1>
        <p>Discover endless opportunities to earn extra income with ease—post your skills or find side jobs that fit your hustle.</p>
        <div class = "searchbar">
            <input type = "text" placeholder="Search...">
        </div>
        </div>
    </div>
    <div class= "illustration">
        <img src="../imgs/character.png" alt="character.png">
    </div>
</div>
</section>
<section class = "tag">
    <div class="about">
        <h1>Our Story</h1>
        <p>SideHustl was created with the vision of empowering hustlers of all ages, backgrounds, and locations across the country. Our mission is to provide accessible opportunities for individuals to earn, create, and grow, no matter where they are in their journey. We believe in unlocking potential by offering a space where people can explore their passions, develop new skills, and tap into their creativity—all from the comfort of home. Whether you’re a seasoned professional looking to diversify your income or someone eager to start your first side gig, SideHustl is the platform where ambition meets opportunity. Here, you can easily find or post jobs, connect with others, and turn your dreams into reality—because we know that everyone has something valuable to offer. SideHustl is more than just a job platform; it’s a community designed to help you grow and succeed at your own pace.</p>
    </div>
</section>
<section class="tag horizontal">
    <div class="faqs">
        <h1>Frequently Asked Questions</h1>
    </div>
        <div class="faq-container">
            <div class="card">
                <h4 class="card-title"><b>Is there an age limit to join SideHustl?</b></h4> 
                <p><br>No! SideHustl welcomes hustlers of all ages. Whether you're a student, a professional, or a retiree, there's a hustle waiting for you.</p>
            </div>
            <div class="card">
                <h4 class="card-title"><b>Do I need any specific skills to use SideHustl?</b></h4> 
                <p><br>Not at all! Whether you're a skilled professional or just looking for simple gigs, there’s something for everyone on SideHustl.</p>
            </div>
            <div class="card">
                <h4 class="card-title"><b>Are there any fees to use SideHustl?</b></h4> 
                <p><br>As of date, creating an account and browsing jobs or services is completely free.</p>
            </div>
            <div class="card">
                <h4 class="card-title"><b>How do I find jobs that match my skills?</b></h4> 
                <p><br>You can use our advanced search filters to find jobs based on category, location, or pay. You’ll also get recommendations tailored to your profile.</p>
            </div>
            <div class="card">
                <h4 class="card-title"><b>How do I ensure safe transactions?</b></h4> 
                <p><br>We prioritize safety by offering secure payment options and verified profiles. Always communicate through the platform for added security.</p>
            </div>
            <div class="card">
                <h4 class="card-title"><b>Can I work remotely through SideHustl?</b></h4> 
                <p><br>Absolutely! Many opportunities on SideHustl are designed for remote work, so you can hustle right from your home.</p>
            </div>
        </div>
        <!-- <div class="footnote">

        </div> -->
</section>
</body>
</html>