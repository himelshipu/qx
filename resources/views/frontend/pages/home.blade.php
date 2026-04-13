@extends('frontend.layouts.app')

@section('content')
	<div class="min-h-screen transition-colors duration-200">
		<main class="container mx-auto flex flex-col gap-14">

			<x-frontend.navigation.hero />
			<x-frontend.partials.featured :featuredInfluencers="$featuredInfluencers" />
			<x-frontend.partials.social-media :influencersByPlatform="$influencersByPlatform" />
			<x-frontend.partials.cases />
			<x-frontend.partials.testimonials :testimonials="$testimonials" />
			<x-frontend.partials.search />
			<x-frontend.partials.campaign />
			<x-frontend.partials.categories />
			<x-frontend.partials.faq :faqItems="$faqItems" />
			<x-frontend.partials.trusted-reviews />
			<x-frontend.partials.cta />



		</main>
	</div>
@endsection
