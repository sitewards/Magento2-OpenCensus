# Magento OpenCensus

## Project Outline

This is the Magento 2 opencensus project. It integrates these two projects together:

- Magento 2: An open source e-commerce platform
- OpenCensus: An implementation of Transaction Tracing

## Similar Work

- The author knows of some work to integrate the profiler with flame graphs.

## Justification

Transaction Traces allow getting complex performance data from an application in an easy to navigate format.

## Installation

This requires an additional patch to the bootstrap file. This has been submitted as a pull request against Magento 2,
but is included below for illustrative purposes:

```
diff --git a/app/bootstrap.php b/app/bootstrap.php
index e77c6d432c8..b3f2c71f0f8 100644
--- a/app/bootstrap.php
+++ b/app/bootstrap.php
@@ -54,12 +54,18 @@ if (
     && isset($_SERVER['HTTP_ACCEPT'])
     && strpos($_SERVER['HTTP_ACCEPT'], 'text/html') !== false
 ) {
-    $profilerFlag = isset($_SERVER['MAGE_PROFILER']) && strlen($_SERVER['MAGE_PROFILER'])
+    $profilerString = isset($_SERVER['MAGE_PROFILER']) && strlen($_SERVER['MAGE_PROFILER'])
         ? $_SERVER['MAGE_PROFILER']
         : trim(file_get_contents(BP . '/var/profiler.flag'));

-    \Magento\Framework\Profiler::applyConfig(
-        $profilerFlag,
+    if ($profilerString && $profilerArray = json_decode($profilerString, true)) {
+        $profilerConfig = $profilerArray;
+    } else {
+        $profilerConfig = $profilerString;
+    }
+
+    Magento\Framework\Profiler::applyConfig(
+        $profilerConfig,
         BP,
         !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'
     );
```

See https://github.com/magento/magento2/pull/15171 for details. More generally, this allows injecting complex
configuration to the bootstrap process. It might not be 100% secure -- use with caution.

However, once the patch above is applied, the following configuration should enable the profiler:

```json
{"drivers":{"type":"Sitewards\\OpenCensus\\Profiler\\Driver\\OpenCensus"}}
```
## Usage:

Probably don't. It's still very experimental.

## Thanks

- David Manners
- The Commwrap Team

