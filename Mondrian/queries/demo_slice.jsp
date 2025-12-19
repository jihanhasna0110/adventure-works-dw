<%@ page session="true" contentType="text/html; charset=ISO-8859-1" %>
<%@ taglib uri="http://www.tonbeller.com/jpivot" prefix="jp" %>

<jp:mondrianQuery
    id="querydemo2"
    jdbcDriver="com.mysql.cj.jdbc.Driver"
    jdbcUrl="urlmu"
    jdbcUser="usernamemu"
    jdbcPassword="passwordmu">
    catalogUri="/WEB-INF/queries/adventureworks_schema.xml">

SELECT 
  {[Measures].[Total Sales], [Measures].[Order Count]} ON COLUMNS,
  [Product].[Category].Members ON ROWS
FROM [Sales]


</jp:mondrianQuery>
