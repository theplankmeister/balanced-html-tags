# Balanced HTML Tags

A simple HTML validator that checks that tags are balanced. Use the two available static methods:

```php
$isBalanced = BalancedHtmlTags::balancedTags('<div>one<p></div>'); //false
$balanced = BalancedHtmlTags::balancedTags('<div>one<p></div>'); //<div>one<p></p></div>
```
### Version
1.0.0

License
----

MIT
