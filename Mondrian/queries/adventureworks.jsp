<%@ page session="true" contentType="text/html; charset=ISO-8859-1" %>
<%@ taglib uri="http://www.tonbeller.com/jpivot" prefix="jp" %>

<jp:mondrianQuery
    id="query01"
    catalogUri="/WEB-INF/queries/adventureworks_schema.xml"
    jdbcDriver="com.mysql.cj.jdbc.Driver"
    jdbcUrl="jdbc:mysql://localhost:3306/adventureworks_dw?useSSL=false&serverTimezone=UTC"
    jdbcUser="root"
    jdbcPassword="">

  SELECT 
    {[Measures].[Total Sales], [Measures].[Order Count]} ON COLUMNS,
    [Time].[Year].Members ON ROWS
  FROM [Sales]

</jp:mondrianQuery>

<jp:table id="table01" query="query01"/>
