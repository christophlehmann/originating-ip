# TYPO3 Extension: X-Originating-IP

## What does it do?

Add the `X-Originating-IP` header to mails, that are generated in the frontend by unauthenticated users. Its value is the client IP address.

## What is it used for?

The header is used for doing spam checks like blocklist lookups. For privacy reasons it's common practice to drop the header afterward.