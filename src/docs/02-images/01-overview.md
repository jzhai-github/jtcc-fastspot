# Image Sizes

We will be referencing image sizes based on a key (such as `wide, portraitClassic, etc`), which can be referenced from this file _(Reference is Below this list)_:

`./image-sizes.json`

| Component                                      | Sizes                                                                         |
| ---------------------------------------------- | ----------------------------------------------------------------------------- |
| [Spotlight][component-spotlight]               | `portraitClassic.sml, classic.med, ultrawide.lrg`                             |
| [Contact Info][component-contact-info]         | `square.xxsml, square.thumb`                                                  |
| [Media Grid - 1/2 Width][component-media-grid] | `wide.med, wide.sml, wide.xsml`                                               |
| [Media Grid - 1/4 Width][component-media-grid] | `portraitClassic.med, portraitClassic.sml, portraitClassic.xsml`              |
| [Media Grid - 3/4 Width][component-media-grid] | `wide.med, wide.sml, wide.xsml`                                               |
| [News By][component-news-by]                   | `classic.sml, classic.xsml, classic.xxsml`                                    |
| [Story][component-story]                       | `classic.med, classic.sml`                                                    |
| [Testimonial - Mobile][component-testimonial]  | `full.med, full.sml`                                                          |
| [Testimonial - Desktop][component-testimonial] | `portraitFull.med, portraitFull.lrg, portraitFull.xlrg`                       |
| [Topic Row][component-topic-row]               | `classic.sml, classic.xsml, classic.xxsml`                                    |
| [Contact Header][component-contact-header]     | `square.sml, square.xsml, square.xxsml`                                       |
| [Event Item][component-event-item]             | `classic.med, classic.sml, classic.xsml, classic.xxsml`                       |
| [News Item][component-news-item]               | `classic.med, classic.sml, classic.xsml, classic.xxsml`                       |
| [Page Header BG][component-page-header-bg]     | `ultrawide.xlrg, ultrawide.lrg, ultrawide.med, ultrawide.sml, ultrawide.xsml` |

# Reference

```json
{
	"ultrawide": {
		"xxsml": {
			"width": 300,
			"height": 129
		},
		"xsml": {
			"width": 500,
			"height": 214
		},
		"sml": {
			"width": 740,
			"height": 317
		},
		"med": {
			"width": 980,
			"height": 420
		},
		"lrg": {
			"width": 1220,
			"height": 523
		},
		"xlrg": {
			"width": 1440,
			"height": 617
		}
	},
	"wide": {
		"xxsml": {
			"width": 300,
			"height": 169
		},
		"xsml": {
			"width": 500,
			"height": 282
		},
		"sml": {
			"width": 740,
			"height": 416
		},
		"med": {
			"width": 980,
			"height": 552
		},
		"lrg": {
			"width": 1220,
			"height": 686
		},
		"xlrg": {
			"width": 1440,
			"height": 810
		}
	},
	"full": {
		"xxsml": {
			"width": 300,
			"height": 225
		},
		"xsml": {
			"width": 500,
			"height": 375
		},
		"sml": {
			"width": 740,
			"height": 555
		},
		"med": {
			"width": 980,
			"height": 735
		},
		"lrg": {
			"width": 1220,
			"height": 915
		},
		"xlrg": {
			"width": 1440,
			"height": 1080
		}
	},
	"square": {
		"thumb": {
			"width": 100,
			"height": 100
		},
		"xxsml": {
			"width": 300,
			"height": 300
		},
		"xsml": {
			"width": 500,
			"height": 500
		},
		"sml": {
			"width": 740,
			"height": 740
		},
		"med": {
			"width": 980,
			"height": 980
		}
	},
	"classic": {
		"xxsml": {
			"width": 300,
			"height": 200
		},
		"xsml": {
			"width": 500,
			"height": 334
		},
		"sml": {
			"width": 740,
			"height": 494
		},
		"med": {
			"width": 980,
			"height": 654
		},
		"lrg": {
			"width": 1220,
			"height": 814
		},
		"xlrg": {
			"width": 1440,
			"height": 960
		}
	},
	"portraitFull": {
		"xxsml": {
			"width": 225,
			"height": 300
		},
		"xsml": {
			"width": 375,
			"height": 500
		},
		"sml": {
			"width": 555,
			"height": 740
		},
		"med": {
			"width": 735,
			"height": 980
		},
		"lrg": {
			"width": 915,
			"height": 1220
		},
		"xlrg": {
			"width": 1080,
			"height": 1440
		}
	},
	"portraitClassic": {
		"xxsml": {
			"width": 200,
			"height": 300
		},
		"xsml": {
			"width": 334,
			"height": 500
		},
		"sml": {
			"width": 494,
			"height": 740
		},
		"med": {
			"width": 654,
			"height": 980
		},
		"lrg": {
			"width": 814,
			"height": 1220
		},
		"xlrg": {
			"width": 960,
			"height": 1440
		}
	}
}
```

[component-spotlight]: {{ "/components/detail/component-spotlight" | path }}
[component-contact-info]: {{ "/components/detail/component-contact-info" | path }}
[component-media-grid]: {{ "/components/detail/component-media-grid" | path }}
[component-news-by]: {{ "/components/detail/component-news-by" | path }}
[component-story]: {{ "/components/detail/component-story" | path }}
[component-testimonial]: {{ "/components/detail/component-testimonial" | path }}
[component-topic-row]: {{ "/components/detail/component-topic-row" | path }}
[component-contact-header]: {{ "/components/detail/partial-contact-header" | path }}
[component-event-item]: {{ "/components/detail/partial-event-item" | path }}
[component-news-item]: {{ "/components/detail/partial-news-item" | path }}
[component-page-header-bg]: {{ "/components/detail/partial-page-header-bg" | path }}
