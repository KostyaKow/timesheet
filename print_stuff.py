#!/usr/bin/python

import pprint, json

def f():
    with open('test.json', 'r') as f:
        return json.loads(f.read())

pp = pprint.PrettyPrinter()
pp.pprint(f())
