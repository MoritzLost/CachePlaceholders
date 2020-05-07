# Cacheable Placeholders for ProcessWire

This is a [ProcessWire](https://processwire.com/) module that enables you to include dynamic content in cached page output. This works by including special placeholders inside your source code that are replaced with dynamic content for every request. This works with both the template cache and the `$cache` API. The module comes with two built-in placeholders – one for CSRF tokens (dynamic by nature), the other one for retrieving dynamic values from a superglobal (like the current session). You can also define your own placeholders with a callback that generates the dynamic output. The module can be used in automatic mode, which automatically replaces placeholders during every request (even when the response comes from the template cache) or manually by using the replaceTokens method on specific pieces of cached data.

## Table of contents

## Motivation and breakdown

Caching is a huge factor for page speed. Caching the output means most of your template code doesn't need to run for every request, which decreases server load and increases response times. Unfortunately, caching becomes difficult if we need dynamic content in our pages, i.e. content that depends on some variable that is not stored inside a page field, like the current user, session or time. For example, let's say you want to have a personal greeting on your site that includes the name of the current user which is stored in the session. If you want to do this on the server-side (using JavaScript comes with it's own set of problems), you run into a problem with the cache. If we show Bob a page that was cached during a page visit by Alice, Bob will see `Hello Alice!` instead of `Hello Bob!`. This means that you can't really use the [template cache](https://processwire.com/docs/start/structure/templates/#other-page-settings-managed-by-templates) anymore, because that caches page output wholesale. You can still use the [$cache API](https://processwire.com/api/ref/wire-cache/) to cache individual sections of your page output, you just have to be careful not to cache the section that includes the greeting. Now this problem gets worse if the greeting (or any piece of dynamic content) is not placed in a predictable place in the templates, but instead used as a [Hanna Code shortcode](https://modules.processwire.com/modules/process-hanna-code/) in a textarea. At this point, the greeting may appear anywhere on your page, so you can't really cache large sections anymore. This necessitates a much more granular caching approach, which will be more complicated and less efficient. Another solution is to vary your cache keys by whatever is a source of variance in the template – for example, the current user ID. But that results in far fewer cache hits, because a cached response created for Alice can't be served to Bob.

This module aims to solve that by introducing placeholders into your cached content that get replaced dynamically on each request. This way, only the code that actually needs to run during every request does so, while everything else can be cached. I'll refer to those placeholders as *cache tokens* troughout the documentation. This module is intended as a developer tool with a focus on registering your own cache tokens. For this purpose, the module provides the following:

- A hookable method you can use add your own callbacks
- A method to perform token replacements manually, as well as an automatic mode that replaces tokens in the page output of every request.
- A couple of built-in tokens for common tasks.
- ...

### Why not use Hanna Code or any existing tag parser?

## Basic usage

After installing the module

### Example 1: Personal greeting

### Example 2: Random number
