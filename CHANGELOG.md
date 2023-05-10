# Change Log

## [v0.1.2](https://github.com/ubleipzig/tx-supportchat-stats/tree/0.1.2)

[Full Changelog](https://github.com/ubleipzig/tx-supportchat-stats/compare/0.1.1...0.1.2)

* **Fixes:** 
  * fixes deprecated @inject annotation cmp. [82869](https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/9.0/Feature-82869-ReplaceInjectWithTYPO3CMSExtbaseAnnotationInject.html)
  * fixes getting module name due to empty GET parameter 'M'
    * add new static method _getModuleName_ at _Classes/Controller/StatsController.php_

## [v0.1.1](https://github.com/ubleipzig/tx-supportchat-stats/tree/0.1.1)

[Full Changelog](https://github.com/ubleipzig/tx-supportchat-stats/compare/0.1.0...0.1.0)

* **Improvement:** Display notice if no data available for chat history

## [v0.1.0](https://github.com/ubleipzig/tx-supportchat-stats/tree/0.1.0)

* **Add:** Chat history
    * Displays chat protocols
    * Linking via paging and table of chats sorted by day and attendees
* **Add:** Chats statistic
    * View for chats per year, month, weekday and hour
    * Period selecting for view of chat per month, weekday and hour
