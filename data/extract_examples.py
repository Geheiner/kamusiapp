#!/usr/bin/python3

'''

This script extracts example sentences from wordnet database files.
The wordnet database files can be found on:

    http://wordnet.princeton.edu/wordnet/download/

For the kamusi game we are using v3.1 of the database

The needed files are:

    data.adv
    data.adj
    data.noun
    data.verb

and they should be placed in the same folder as this script.

The output produced by this script is a single file named examples
that contains a line for every example consisting of:

    'synset_offset' 'ss_type' example

where example is part of the 'gloss' section in the data files.
For more information consult the wordnet documentation:

    http://wordnet.princeton.edu/wordnet/man/wndb.5WN.html

'''

import re, sys

def extract_examples(line):
    # examples are between double quotes
    # so we extract all groups of double quotes
    examples = re.findall(r'(\".+?\")', line)
    return examples


output = open("examples", 'w')
file_suffixes = ['adj', 'adv', 'noun', 'verb']
max_len = 0

for suffix in file_suffixes:
    with open("data."+suffix, "r") as lines:
        # Skip license agreement (first 29 lines)
        for i in range(0, 29):
            next(lines)
        for line in lines:
            _id = line[:8]
            part_of_speech = line[12]
            examples = extract_examples(line)
            for example in examples:
                if len(example) > max_len:
                    max_len = len(example)
                output.write(_id)
                output.write('\t')
                output.write(part_of_speech)
                output.write('\t')
                # remove double quotes
                output.write(example[1:-1])
                output.write('\n')
output.close()
print("# of characters of longest example: %d" % max_len)
