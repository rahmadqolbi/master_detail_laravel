<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>Customers Report</title>
  <link rel="stylesheet" type="text/css" href="stimulsoft-report/2021.3.6/css/stimulsoft.viewer.office2013.whiteblue.css">
  <link rel="stylesheet" type="text/css" href="stimulsoft-report/2021.3.6/css/stimulsoft.designer.office2013.whiteblue.css">
  <script type="text/javascript" src="stimulsoft-report/2021.3.6/scripts/stimulsoft.reports.js"></script>
  <script type="text/javascript" src="stimulsoft-report/2021.3.6/scripts/stimulsoft.viewer.js"></script>
  <script type="text/javascript" src="stimulsoft-report/2021.3.6/scripts/stimulsoft.dashboards.js"></script>
  <script type="text/javascript" src="stimulsoft-report/2021.3.6/scripts/stimulsoft.designer.js"></script>
  <script type="text/javascript">
    function Start() {
      Stimulsoft.Base.StiLicense.loadFromFile("stimulsoft-report/2021.3.6/stimulsoft/license.key");

      var viewer = new Stimulsoft.Viewer.StiViewer(null, "StiViewer", false)
      var report = new Stimulsoft.Report.StiReport()

      var options = new Stimulsoft.Designer.StiDesignerOptions()
      options.appearance.fullScreenMode = true

      // var designer = new Stimulsoft.Designer.StiDesigner(options, "Designer", false)

      var dataSet = new Stimulsoft.System.Data.DataSet("Data")

      viewer.renderHtml('content')
      report.loadFile('report/reportDetail (1).mrt')

      report.dictionary.dataSources.clear()

      dataSet.readJson(<?php echo $dataTotal ?>)

      report.regData(dataSet.dataSetName, '', dataSet)
      report.dictionary.synchronize()

      viewer.report = report
      designer.renderHtml("content")
      designer.report = report
    }

    function afterPrint() {
      if (confirm('Tutup halaman?')) {
        window.close()
      }
    }
  </script>
</head>
<body onload="Start()" onafterprint="afterPrint()">
  <div id="content"></div>
</body>
</html>

