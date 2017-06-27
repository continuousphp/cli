SHELL := /bin/bash

TAG=$(shell echo ${CPHP_GIT_REF} | tail -c +11)
PHAR_NAME="continuousphp-$(TAG).phar"

cphp-gh-phar:
	@echo "Attach phar to github release: $(PHAR_NAME)"
