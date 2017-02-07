# RegDom - Registered Domain Check in PHP

Object oriented adaptation of Florian Sager's regdom library for querying
Mozilla's Public Suffix List,  complete with unit testing.

For more information of public suffixes, and why you may want to query
the list, please see [publicsuffix.org](https://publicsuffix.org/)

# Installation

> composer require geekwright/regdom

# Usage

## Class Geekwright\RegDom\PublicSuffixList
This class handles all direct interaction with the public suffix list (PSL.)
This includes, fetching the list from a source URL, caching that list
locally, converting that list to a tree form to facilitate rapid lookup,
and caching that tree in a serialized form.

### $psl = new PublicSuffixList(*$url*)
Creates a new PublicSuffixList object that will use the specified URL as
the source of the PSL. If no *$url* is specified, it will default to
https://publicsuffix.org/list/public_suffix_list.dat

### $psl->setURL(*$url*)
Resets the current PSL, and uses the specified URL as the source.

### $psl->getTree()
Returns the tree of the current PSL. `Geekwright\RegDom\RegisteredDomain`
uses this tree form for all lookups.

### $psl->clearDataDirectory(*$cacheOnly*)
By default, this will clear all cached PSL data, including local copies
of remotely accessed PSL data. Pass *true* for `$cacheOnly` to clear only
the serialized tree data.

## Class Geekwright\RegDom\RegisteredDomain
This class can be used to determine the registrable domain portion of a
URL, respecting the public suffix list conventions.

### $regdom = new RegisteredDomain(PublicSuffixList *$psl*)
Creates a new RegisteredDomain object that will use *$psl* to access the
PSL. If no *$psl* is specified, a new PublicSuffixList object will be
instantiated using default behaviors.

### $regdom->getRegisteredDomain(*$host*)
Returns the shortest registrable domain portion of the supplied *$host*,
or *null* if the host could not be validly registered.

Examples:
`echo $regdom->getRegisteredDomain('https://www.google.com/');`
Outputs:
> google.com

`echo $regdom->getRegisteredDomain('theregister.co.uk');`
Outputs:
> theregister.co.uk

`echo null === $regdom->getRegisteredDomain('co.uk');`
Outputs:
> 1

## script bin\reloadpsl
This script can be used to load a fresh copy of the PSL. It may be useful
in cron job, or other scripted updates.

# Credits

Reg-dom was written by Florian Sager, 2009-02-05, sager@agitos.de

Original code was published at http://www.dkim-reputation.org/regdom-lib-downloads/

Marcus Bointon's adapted code is here http://github.com/Synchro

# Original License

    # Licensed to the Apache Software Foundation (ASF) under one or more
    # contributor license agreements.  See the NOTICE file distributed with
    # this work for additional information regarding copyright ownership.
    # The ASF licenses this file to you under the Apache License, Version 2.0
    # (the "License"); you may not use this file except in compliance with
    # the License.  You may obtain a copy of the License at:
    #
    #     http://www.apache.org/licenses/LICENSE-2.0
    #
    # Unless required by applicable law or agreed to in writing, software
    # distributed under the License is distributed on an "AS IS" BASIS,
    # WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
    # See the License for the specific language governing permissions and
    # limitations under the License.

