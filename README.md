# Balanced HTML Tags

A simple HTML validator that checks that tags are balanced. Use the two available static methods:

```php
$isBalanced = BalancedHtmlTags::tagsBalanced('<div>one<p></div>'); //false
$balanced = BalancedHtmlTags::balanceTags('<div>one<p></div>'); //<div>one<p></p></div>
```
### Version
1.0.0

License
----

MIT
