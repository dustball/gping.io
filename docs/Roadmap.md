# GPing Project Roadmap

<a name="b1"></a>
Automative diagnostics and car tracking offer an intimate view to our daily
activity. It's no surprise, then, that several products<sup>[1](#es)</sup>
have been developed utilizing this information to provide some novel features.
It's also unsurprising that people may be uncomfortable giving access to this
data to a third party who's primary goal is to make a profit.

The GPing project aims to become a flexible server capable of supporting geo
tracking applications with an initial goal of feature parity to existing
products available in the connected car space. To that our work will be focused
on building out the following feature areas:

1. Authentication and provisioning;
1. API data access;
1. Geofence definition / alerting;
1. Web dashboards and live maps;
1. Subscribable event streams.

## Building GPing

Our top two priorities have milestones ([auth][ms-auth] and [api][ms-api]) set
up in GitHub and represent the majority of work in progress. New contributors
are welcome to send PRs directly or contact us ([email][listserv] or #gping.io
on Freenode)) for help finding a way to contribute suited to their comfort
level.

[ms-auth]: https://github.com/dustball/gping.io/milestone/1
[ms-api]: https://github.com/dustball/gping.io/milestone/2
[listserv]: https://groups.google.com/forum/#!forum/gpingio

---

1.  <a name="#es"></a>Some actors in the connected car space are
[Automatic][auto], [Zubie][zubie], [Hum][hum], and [Car Connection][avox]. Most
of these share a similar feature set though Automatic stands a bit above the
rest.  
<!-- -->  
Additionally, a few services focus on car-service help earning on "honorable
mention" due to their related but more limited nature: Progressive's
[Snapshot][ps], [Urgent.ly][urgent], and [Honk][honk].  [^](#b1)

[auto]: http://www.automatic.com
[zubie]: http://zubie.com
[avox]: http://www.mycar-connection.com/
[hum]: http://www.hum.com
[ps]: http://www.progressive.com/auto/snapshot
[urgent]: http://www.geturgently.com
[honk]: http://www.honkforhelp.com
