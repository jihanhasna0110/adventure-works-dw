<%@ page session="true" contentType="text/html; charset=ISO-8859-1" %>
<%@ taglib uri="http://www.tonbeller.com/jpivot" prefix="jp" %>

<jp:mondrianQuery
    id="query_bq3"
    catalogUri="/WEB-INF/queries/adventureworks_schema.xml"
    jdbcDriver="com.mysql.cj.jdbc.Driver"
    jdbcUrl="urlmu"
    jdbcUser="usernamemu"
    jdbcPassword="passwordmu">

  SELECT 
    {[Measures].[Order Count],
     [Measures].[Total Sales],
     [Measures].[Avg Order Value]} ON COLUMNS,
    NON EMPTY [Order Status].[Status].Members ON ROWS
  FROM [Sales]
  WHERE ([Time].[Year].[2004])

</jp:mondrianQuery>

