<!-- Head section -->
 @include('layouts.inc.header')
<!-- end Head section -->

<!-- Nav section -->
@include('layouts.inc.nav')
<!-- end Nav section -->

<!-- slider section -->
@if(in_array('slider', $enabledSections))
@include('sections.slider')
@endif
<!-- end slider section -->

<!-- shop section -->
@if(in_array('Latest Products', $enabledSections))
@include('sections.latest', ['products' => $products])
@endif
<!-- end shop section -->

<!-- saving section -->
@if(in_array('Saving Banner', $enabledSections))
@include('sections.saving')
@endif
<!-- end saving section -->

<!-- why section -->
@if(in_array('Why Section', $enabledSections))
@include('sections.why')
@endif
<!-- end why section -->

<!-- gift section -->
@if(in_array('Gift Banner', $enabledSections))
@include('sections.gift')
@endif
<!-- end gift section -->

<!-- contact section -->
@if(in_array('Contact Section', $enabledSections))
@include('sections.contact')
@endif
<!-- end contact section -->

<!-- client section -->
@if(in_array('Top User Section', $enabledSections))
@include('sections.user')
@endif
<!-- end client section -->

<!-- Footer section -->
@include('layouts.inc.footer')
<!-- end Footer section -->