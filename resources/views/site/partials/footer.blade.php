<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div>
                <a href="{{ route('home') }}" class="footer-logo">
                    @if (setting('logo'))<img src="{{ upload_url(setting('logo_light') ?: setting('logo')) }}" alt="" width="60" height="48" loading="lazy">@endif
                    <span>{{ setting('site_name') }}</span>
                </a>
                <p>{{ setting('footer_about') }}</p>
                @if (setting('legal_name'))<p class="small mb-3"><i class="bi bi-patch-check me-1"></i>{{ setting('legal_name') }}</p>@endif
                @include('site.partials.social', ['class' => 'social'])
            </div>
            <div>
                <h2>Explore</h2>
                <ul>
                    <li><a href="{{ route('about') }}">About Us</a></li>
                    <li><a href="{{ route('programs.index') }}">Our Programs</a></li>
                    <li><a href="{{ route('campaigns.index') }}">Campaigns</a></li>
                    <li><a href="{{ route('events.index') }}">Events</a></li>
                    <li><a href="{{ route('blog.index') }}">Blog &amp; News</a></li>
                    <li><a href="{{ route('gallery') }}">Gallery</a></li>
                    <li><a href="{{ route('team') }}">Our Team</a></li>
                    <li><a href="{{ route('faq') }}">FAQ</a></li>
                </ul>
            </div>
            <div>
                <h2>Get involved</h2>
                <ul>
                    <li><a href="{{ route('donate') }}">Donate Now</a></li>
                    <li><a href="{{ route('volunteer') }}">Volunteer</a></li>
                    <li><a href="{{ route('membership') }}">Membership</a></li>
                    <li><a href="{{ route('partnership') }}">Partner With Us</a></li>
                    <li><a href="{{ route('careers') }}">Careers</a></li>
                    <li><a href="{{ route('internships') }}">Internships</a></li>
                    <li><a href="{{ route('transparency') }}">Transparency</a></li>
                    <li><a href="{{ route('reports') }}">Annual Reports</a></li>
                </ul>
            </div>
            <div>
                <h2>Contact</h2>
                <ul class="gap-3">
                    <li class="d-flex gap-2"><i class="bi bi-geo-alt text-warning"></i><address class="mb-0">{!! nl2br(e(full_address(true))) !!}</address></li>
                    <li class="d-flex gap-2"><i class="bi bi-telephone text-warning"></i><a href="tel:{{ phone_link(setting('phone')) }}">+91 {{ setting('phone') }}</a></li>
                    <li class="d-flex gap-2"><i class="bi bi-envelope text-warning"></i><a href="mailto:{{ setting('email') }}" class="text-break">{{ setting('email') }}</a></li>
                    @if (setting('office_hours'))<li class="d-flex gap-2"><i class="bi bi-clock text-warning"></i><span>{{ setting('office_hours') }}</span></li>@endif
                </ul>
                <a href="{{ route('donate') }}" class="btn btn-accent mt-4"><i class="bi bi-heart-fill"></i> Support our work</a>
            </div>
        </div>
        <div class="footer-bottom">
            <span>&copy; {{ date('Y') }} {{ setting('site_name') }}. All rights reserved. {{ setting('footer_note') }}</span>
            <span class="d-flex flex-wrap gap-3">
                <a href="{{ route('privacy') }}">Privacy Policy</a>
                <a href="{{ route('terms') }}">Terms &amp; Conditions</a>
                <a href="{{ route('refund') }}">Refund Policy</a>
                <a href="{{ route('sitemap') }}">Sitemap</a>
            </span>
        </div>
    </div>
</footer>
