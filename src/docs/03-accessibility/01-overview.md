All pages of your site are run through a tool called [Axe][axe]. `Axe` is testing software that recommends best accessibility practices for pages on your website.

Your site has been run through `Axe` and has been deemed accessible per accessibility standards.

# What pages do we test?

We test all the pages found in the `Templates` section of `Fractal`. This ensure every template type on your site meet accessibility standards for your users.

Here are some templates we have tested for your site (to name a few):

- [Home Page][page-home]
- [General Content][page-general-content]
- [Program Listing][page-program-listing]
- [Program Detail][page-program-detail]

The accessibility test can be found under ["Accessibility > Axe" inside Fractal][page-axe].

## False Positives

### Homepage

You'll notice a violation for, _"Ensures the contrast between foreground and background colors..."_. Upon further manual testing, this text does pass accessibilility and can be ignored.

[page-home]: {{ "/components/preview/template-homepage" | path }}
[page-general-content]: {{ "/components/preview/template-general-content-image" | path }}
[page-program-listing]: {{ "/components/preview/template-program-listing" | path }}
[page-program-detail]: {{ "/components/preview/template-program-detail-image" | path }}
[page-axe]: {{ "/components/preview/axe" | path }}
[axe]: https://www.deque.com/axe/
