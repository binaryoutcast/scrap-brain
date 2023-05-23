<?php /****************************************************************************************************************
* PHP Code is filmed before a live studio audience.
**********************************************************************************************************************/

// == | Main | ========================================================================================================

$ilFilesURL = 'https://projects.binaryoutcast.com/interlink/releases/latest/';
$ilAddonsURL = 'https://interlink-addons.binaryoutcast.com/';
$codeAtGithubURL = 'https://github.com/binaryoutcast/';

function xGenerateAFuckinBinOCWebsite($aContent = null, $aPageTitle = null, $aHeaderTitle = null, $aHeaderTagline = null) {
  $contentPath = gRegistry('app.componentPath') . kSlash . 'content';
  $skinPath = gAppUtils::StripStr(gRegistry('app.componentPath'), kRootPath) . kSlash . 'skin';
  $template = gReadFile(gBuildPath(kRootPath, $skinPath, 'template.html'));
  if (!$template) { gError('There is no spoon.. I mean template.'); }
  $aPageTitle = $aPageTitle ?? ucfirst(str_replace(kDash, kSpace, gGetConfig('app.path.0')));
  $aContent = $aContent ?? '<p>No content supplied.</p>';
  $taglineFallback = 'Exploring the' . kSpace . strtolower($aPageTitle) . kSpace . 'page' . kDot;

  $substs = array(
  //'xtplStyleSheet'      => $stylesheet ?? kEmptyString,
    'xtplContentBody'     => $aContent,
    'xtplComnSkinPath'    => '/base/skin',
    'xtplCompSkinPath'    => gAppUtils::StripStr($skinPath, kRootPath),
  //'xtplContentPath'     => gAppUtils::StripStr($contentPath, kRootPath),
    'xtplSiteName'        => gGetConfig('console.content.siteName', kAppName),
    'xtplAppPath0'        => gGetConfig('app.path.0', kEmptyString),
    'xtplAppPath1'        => gGetConfig('app.path.1', kEmptyString),
    'xtplAppPath2'        => gGetConfig('app.path.2', kEmptyString),
    'xtplAppPath3'        => gGetConfig('app.path.3', kEmptyString),
    'xtplPageTitle'       => $aPageTitle,
    'xtplPageSection'     => gGetConfig('app.path.0', kEmptyString),
    'xtplHeaderTitle'     => $aHeaderTitle ?? $aPageTitle,
    'xtplHeaderTagline'   => $aHeaderTagline ?? $taglineFallback,
    'xtplCurrentYear'     => date("Y"),
  );

  gOutput(gSubstEx($template, $substs), 'html');
}

// --------------------------------------------------------------------------------------------------------------------

gRegSet('console.content.siteName', 'Binary Outcast');

switch (gGetConfig('app.path.0')) {
  case 'projects':
    (gGetConfig('app.path.1') == 'interlink') ? gRedirect($ilFilesURL) : gRedirect($codeAtGithubURL);  
    break;
  case 'updates':
    $content = kEmptyString;

    //if (gGetConfig('superglobal.get.preview')) { }
    $content .= '<h2>What? Did BinOC finally run out of that left over vista-acrylic to dip logos into?<small class="alignright" style="font-weight: normal;">2023-05-22</small></h2>';
    $content .= '<p><img src="/components/site/skin/updates/p002-binoc.png" class="alignright" title="Everything old is new again.. OR IS IT?!"/></p>Yes. Yes we did. More seriously though, Binary Outcast is proud to debut the first example of a whole new generation of BinOC Design. This new style and the ones derived from it in the future is anticipated to leapfrog the current design trends to those that have only started emerging or creeping back into the mainstream recently.';
    $content .= '<p>For much of the lifetime of this website it has not been more than a cool web design and a page such as this with others coming and going but most often never materializing. At the risk of the few that stop by assuming this "Stay Tuned" is just like 98% of all the other times, we have decided this re-launch and revamp of Binary Outcast should be done with the upmost consideration and thought. Both for times past and adventures yet to come. Specifically, as to what, how, and why this place does and should continue to exist. This is also why it is so grandiosely stated on the front page that our real work has not even started yet. Still, as I was fond of saying last decade: "All the pieces are there they just need assembling.. correctly". I\'d argue that sums up BinOC pretty well right now.</p>';
    $content .= '<p><img src="/components/site/skin/updates/p002-xpapps.png" class="alignleft" title="Mariner icon (Old Borealis - left) | Interlink icon (right)"/>The kernel of this design change started with the ABPrime fork of Adblock Latitude after the code was reverted when I stepped down from the Pale Moon project at the end of 2016. However, it did not reach an actual design trend for us until Interlink Mail &amp; News was created in 2018. This started the ball rolling on non-acrylic primary branding at BinOC.</p>';
    $content .= '<p>Pictured <small>(to the left)</small> and on the former splash and current front pages is the third revision of the Interlink icon. I originally intended it to be red when I chose design for the background. Though this was later used for the short lived Unstable channel. This bolder refined version of the Icon is more in line with what I had intended and is far less clash-y especially the new wordmark style. Also, it has improved legibility at sizes as small as 16x16 pixels much like the Binary Outcast text at the top at 26px tall <small>(the smallest we could push the old acrylic version, though the B alone works at 16x16)</small>. A constant challenge in raster graphics <small>(the process of how an electron beam is fired to smear light on a screen in an decently organized fashion using the almighty scanline&mdash;<strong>ALL HAIL THE SCANLINES!</strong>)</small> that no designer should ever discount even if they primarily use vector design.</p>';
    $content .= '<h3>You keep using these icons for xul projects that don\'t quite exist yet/anymore as examples. What\'s to be done about that?</h3>';
    $content .= '<p>You are absolutely correct, hypothetical BinOC visitor, that is exactly what I wanted to quickly summarize next. So let\'s break out a couple unordered lists as to status of the Aura-thing-I-call-it .. Runtime Environment .. right!</p>';
    $content .= '<p><strong>Aura Runtime Environment Stuff that works:</strong></p>';
    $content .= '<ul>';
    $content .= '<li>The Codebase has gotten a fresh code infusion from UXP-latest and works.</li>';
    $content .= '<li>Mariner <small>(old Borealis)</small> is re-branded except for the throbber animation but still has half-implemented shellservice integration.</li>';
    $content .= '<li>Every change we have made is also verified as working thus far.</li>';
    $content .= '</ul>';
    $content .= '<p><strong>Aura Runtime Environment Things that don\'t:</strong></p>';
    $content .= '<ul>';
    $content .= '<li>Interlink and calendar do not currently build but are functionally fine. Just build system.</li>';
    $content .= '<li>Some Aura 5.0 advancements have not been ported yet.</li>';
    $content .= '<li>Features and such from UXP-master that are unfinished.</li>';
    $content .= '</ul>';
    $content .= '<p><strong>Bottom line:</strong><br /><br />Mariner could be produced as an <em>unfinished</em> product for distribution fairly quickly but Interlink, the <em>finished</em> product, cannot currently be built due to build system mismatches. Isolated development simply requires a somewhat different approach than I helped foster and became accustom to elsewhere. I am putting a tentative goal on Interlink and public trunk builds of Mariner by my birthday, the first day of summer. However, failing that then July 4th.</p>';
    $content .= '<p>Of course, now that I am committed again I.. best snap to it.<br /><br />Peace!</p>';
    $content .= '<div class="clearfix"></div> <!-- One -->';
    

    $content .= '<h2>Stage 1 - There is a website again.<small class="alignright" style="font-weight: normal;">2023-05-18</small></h2>';
    $content .= '<p><img src="/components/site/skin/splash/labcoat.png" class="alignright" title="The solution to a failed paradigm."/>I wish to give my deepest thanks to all those who are still happening by Binary Outcast. For doing so during the past year or must have been less than inspiring, to say the least. From my perspective, it has been very difficult indeed. However, we (and I do mean more than I), are <em>just about</em> back on track with a lot of the <strong>everything</strong> that we SHOULD have be known for all this time.</p>';
    $content .= '<p>Still, I also want to thank the tireless efforts of the contributors to MCP projects. The ones who have done nothing but work on code, help users, and otherwise just exist for the benefit of everyone, even us. These individuals are the ones to be celebrated even if they no longer feel the same in return. Please go forth and thank them even if you do not use either of our respective offerings.</p>';
    $content .= '<p>While one is doing the rounds one should also take the time to stop by the SeaMonkey Project and thank them. For a lot of their selective MozEvolution on the SM soft-fork platform DOES directly or indirectly benefit the broader <a href="http://thereisonlyxul.org/" target="_blank">MozFork Community</a>. It is also the place where I became interested in HOW Netscape and Classic Mozilla technology worked. Oh yeah, and it is the basis for a lot of the main code specifically used in our XUL Projects.</p>';
    $content .= '<h3>But BinOC\'s in pieces and you\'ve got less than a skeleton crew aboard!</h3><p>Yeah, and? What\'s your point?</p>';
    $content .= '<h3>So sit tight?</h3><p>Pfft.. After 22 years I ain\'t changing it now... Stay tuned!</p>';

    xGenerateAFuckinBinOCWebsite($content, 'News &amp; Updates', null, 'Exciting <small>(and specific)</small> news about Binary Outcast Operations.');
    break;
  case 'about':
    $content = kEmptyString;
    $content .= '<h2>Improvements</h2>';
    $content .= '<p>We are currently combing BinOC History to better document it. It may take a while as the wayback machine and archive searching can be slow work...</a>';
    $content .= '<h2>tl;dr</h2><p>BinOC was created in 2001 to be a place where I, Matt A. Tobin, could exist on the internet without just calling it Matt A. Tobin Dot Com. Originally named TechAcid, the name was changed to Binary Outcast as I was 15 and it was 2001 and words have many nuances.</p>';
    $content .= '<ul>';
    $content .= '<li><strong>2001-2004</strong> - First exists and acts as a personal site.</li>';
    $content .= '<li><strong>2004-2007</strong> - Acts as a Personal and Local Tech Service site.</li>';
    $content .= '<li><strong>2007-2009</strong> - First affiliations resulting in tangible results. Design work. Code by BinOC but published by others. Also personal site bits are moved off BinOC.</li>';
    $content .= '<li><strong>2010-2013</strong> - Acquired Mobile Central and turned it into Tech Central. Also, minor activities in the MozSphere largely in Bug Triage for SeaMonkey.</li>';
    $content .= '<li><strong>2014-2016</strong> - Affiliated with the Pale Moon Project under Moonchild Productions as a contributor and 3rd party build provider. Created and provided the Pale Moon Add-ons Site and other Secondary Services. Later assisting in the creation of the "Tycho" codebase and porting Pale Moon\'s specific code up 14 relative Mozilla releases.</li>';
    $content .= '<li><strong>Also in 2016-2018</strong> - Binary Outcast ceases to exist in any meaningful manner.</li>';
    $content .= '<li><strong>2018-July 2021</strong> - BinOC re-launches as a result of creating a cross-organization, but MCP administrated, meta-project the Unified XUL Platform (including porting Pale Moon\'s UI up another 14 relative release codebases. Also, starts several and launches one slightly successful XUL-application in the form of Interlink Mail &amp; News.</li>';
    $content .= '<li><strong>Aug 2021-March 2022</strong> - BinOC temporarily closes source repos and reverts to tarballs to combat perceived issues at the time. We also work to create the future of UXP known as the MCP Trademark Controlled "Goanna Runtime Environment".</li>';
    $content .= '<li><strong>March 19th of 2022</strong> - Binary Outcast and Matt A. Tobin end all affiliation including direct participation and services/infra provided for any MCP operation or activity.</li>';
    $content .= '<li><strong>The rest of 2022</strong> - Started the Aura Runtime Environment based on fixed GRE code and restored some services. However, other external priorities came into play and BinOC was reduced to a splash screen.</li>';
    $content .= '<li><strong>2023-Today</strong> - Re-Infused the Aura Runtime Environment with UXP-latest as internal priorities and focus shifted (also a unique tree restructure in isolation dependent on another structure which is also half-dependent on a newer structure is terribly inefficient). Also re-launched this Stage 1 Binary Outcast site.</li>';
    $content .= '</ul>';
    xGenerateAFuckinBinOCWebsite($content, null, 'The History of BinOC', 'From 2001 to Today');
    break;
  case 'root':
    $content = '<p><img src="/components/site/skin/splash/promo4.png" class="aligncenter" title="What? Were you expecting a big black monolith because of the copyright date?"></p><p style="text-align: center;"><strong>For 22 years, I <small>(Tobin)</small>, have been asking anyone and everyone to "Stay Tuned" to BinOC. Isn\'t about time we <em>actually</em> got started?</strong></p><hr /><p style="text-align: center;">Check out the <a href="/updates/">BinOC Updates</a> page for exciting <small>(and specific)</small> news!<br /><small>Latest Site Update: 2023-05-22</small></p>';
    xGenerateAFuckinBinOCWebsite($content, 'Front Page (Home)', 'Welcome back, once again, to BinOC!', 'Front Page <small>(Home)</small>');
    break;
  default:
    gNotFound('Page is Zero');
}

// ====================================================================================================================

?>