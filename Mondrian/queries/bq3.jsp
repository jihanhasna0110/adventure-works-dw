<%@ page session="true" contentType="text/html; charset=ISO-8859-1" %>
<%@ taglib uri="http://www.tonbeller.com/jpivot" prefix="jp" %>

<jp:mondrianQuery
    id="query_bq3"
    catalogUri="/WEB-INF/queries/adventureworks_schema.xml"
    jdbcDriver="com.mysql.cj.jdbc.Driver"
    jdbcUrl="jdbc:mysql://localhost:3306/adventureworks_dw?useSSL=false&serverTimezone=UTC"
    jdbcUser="root"
    jdbcPassword="">

  SELECT 
    {[Measures].[Order Count],
     [Measures].[Total Sales],
     [Measures].[Avg Order Value]} ON COLUMNS,
    NON EMPTY [Order Status].[Status].Members ON ROWS
  FROM [Sales]
  WHERE ([Time].[Year].[2004])

</jp:mondrianQuery>
