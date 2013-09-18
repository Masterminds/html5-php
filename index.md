---
layout: page
---
<div class="masthead"><div class="grid-container"><!-- Start Masthead -->
<div class="grid-70 prefix-15 suffix-15">HTML5 Parsing and Writing in PHP is finally here.</div>
</div></div><!-- End Masthead -->
<div class="grid-container highlights"><!-- Start Highlights -->
<div class="grid-50 mobile-grid-100">
  <h2 class="blue">Parsing</h2>
  <p>Parse html5 files, documents, and fragments to standard PHP DOM objects.
</div>
<div class="grid-50 mobile-grid-100">
  <h2 class="blue">Writing (Serializing)</h2>
  <p>Turn standard DOM documents, fragments, and node lists into html5.</p>
</div>
</div>
<div class="grid-container highlights">
<div class="grid-50 mobile-grid-100">
  <h2 class="blue">Configure It</h2>
  <p>Set default and call time options in the basic parser and serializer.</p>
</div>
<div class="grid-50 mobile-grid-100">
  <h2 class="blue">Build Your Own</h2>
  <p>Use the parts to build your own parser or serializer that does what you need.</p>
</div>
</div><!-- End Highlights -->
<div class="grid-container"><div class="grid-100">
<h2>Installation</h2>
<p>The best installation method is via <a href="http://getcomposer.org/">composer</a>. To install add <code>masterminds/html5-php</code> to your <code>composer.json</code> file.</p>
<pre><code>{
  "require" : {
    "masterminds/html5": "dev-master"
  },
}</code></pre>

<p>From there, use the <code>composer install</code> or <code>composer update</code> commands to install.</p>

<h2>Basic Usage</h2>
<pre><code>// An example HTML document:
$html = "&lt;!DOCTYPE html&gt;
&lt;html&gt;
  &lt;head&gt;
    &lt;title&gt;TEST&lt;/title&gt;
  &lt;/head&gt;
  &lt;body id='foo'&gt;
    &lt;h1&gt;Hello World&lt;/h1&gt;
    &lt;p&gt;This is a test of the HTML5 parser.&lt;/p&gt;
  &lt;/body&gt;
&lt;/html&gt;";

// Parse the document. $dom is a DOMDocument.
$dom = \HTML5::loadHTML($html);

print \HTML5::saveHTML($dom);</code></pre>
          
<p>It's that easy. See <a href="https://github.com/Masterminds/html5-php/wiki/Basic-Usage">the documentation</a> for all the wonderful ways to parse and write html5.</p>

</div></div>