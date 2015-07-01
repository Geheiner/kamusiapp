#!/bin/bash
git add --all
git commit -a -m "$1"
git push ec2 master