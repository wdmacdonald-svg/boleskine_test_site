Prompt 1: The Research & Data Mapping Phase (The Browser Agent)
Intent: Establish how the external shinty site passes data and map out the standalone folder structure.

"I want to create automated up to date sports fixtures in the following section of index.html;   <!-- Fixture Card 1 -->. I want  this to populate each of the following sections; Home team first versus Away team, the date, the time and the location. Here are the only lines to concentrate on;
 <!-- Fixture Card 1 -->
                    <div class="news-card reveal-on-scroll">
                        <div class="card-type font-cinzel">Next Match</div>
                        <div class="fixture-details">
                            <div class="fixture-teams">
                                <span class="team-name font-cinzel">Boleskine</span>
                                <span class="vs">vs</span>
                                <span class="team-name font-cinzel">Lovat</span>
                            </div>
                            <div class="fixture-meta">
                                <p><i class="fa-regular fa-calendar-days"></i> Saturday, June 20th</p>
                                <p><i class="fa-regular fa-clock"></i> 2:30 PM BST</p>
                                <p><i class="fa-solid fa-location-dot"></i> Farr Playing Field (Home)</p>
                            </div>
 The only Team Name to be searched for in the fixtures section is Boleskine. If found then populate with the opponents appropriately. If there is no mention of Boleskine in the fixtures section then please indicate "No game this week" Please use the Browser Agent to navigate to matches.shinty.com. Analyze how they render their live match results and identify if there is an accessible data feed, or if we need to write a clean HTML scraper to isolate the Boleskine team and there opponents. The site uses the Themeboy theme for Sportspress. Once analyzed, generate a Task List and Implementation Plan for a lightweight, standalone PHP cron script that will fetch this data and save it to the appropriate sections in "Fixture Card 1" and include time, date and location in fixture meta section. Please abbreviate "Smith Park Inverarnie (home)" to "Smith Pk Inverarnie (H)". Please pause to ask any questions before proceeding further. 



Prompt 2: Writing the Standalone Automation Script (The Coding Agent)
Intent: Programming the isolated PHP bridge that acts as your background worker.

"Create a standalone PHP script in the root directory named fetch-shinty-fixtures.php. This file must act entirely independently of WordPress, which will, when implemented, be running the remainder of the site in a sub-folder. Write the script to perform a secure background request to the matches.shinty.com data target using cURL. Include error handling to check if the remote site is down. Extract the specific data for Boleskine's latest match fixture, format it into a clean JSON structure (containing match date, opponent name (home team first), date, time and location way score, and status), and use file_put_contents() to completely overwrite the local fixtures.json file in the root directory." If this does not make sense to you please pause and ask me for clarification.

Prompt 4: Real System Cron Integration (The Terminal Agent)
Intent: Securing the Linux automation instructions since WP-Cron is no longer involved.

"Since we are not utilizing the WordPress environment for this asset, generate a CRON_SETUP.md document via the File System Agent. Write out the exact path-accurate Linux crontab string needed to execute our standalone fetch-shinty-fixtures.php file directly via the server binary. Set the interval execution pattern to fire every 20 minutes on Saturdays from 15.00 BST to Sundays at 12.00, and once a day during the remainder of the week."

Prompt 5: Visual Verification & Sandbox Testing (The Browser & DevTools Agent)
Intent: Letting Antigravity verify that the data file correctly alters the layout of your new index page.

"Simulate a successful data fetch by populating the local fixtures.json file with mock match results. Then, use the Antigravity Browser Agent to open our standalone index.html page. Inspect the DOM elements to ensure the JavaScript successfully parsed the local file and updated the visual widget elements perfectly without console errors. Capture a verification screenshot artifact of the final rendering."