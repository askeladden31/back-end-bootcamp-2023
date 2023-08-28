Endpoints:  
  
**/collections/create**  
Create new collection, POST request structure:  
{  
  "title": "..."  
  "description": "..."  
  "target_amount": "..."  
  "link": "..."  
}  
  
**/collections/{id}/contribute**  
Contribute to collection with id={id}, POST request structure:  
{  
  "user_name": "..."  
  "amount": "..."  
}  
  
**/collections/{id}**  
Display details for collection with id={id}; GET request  
  
**/collections/**  
List all collections, GET request  
  
Optional parameters:  
  
**/collections?target_not_reached=true**  
"Отримати список зборів, які мають суму внесків менше за цільову суму."  
  
**/collections?remaining_lte={amount}**  
"Реалізувати можливість фільтрування зборів за залишеною сумою до досягнення кінцевої суми." - ***this requirement is quite ambiguously worded. After a long consideration, the interpretation I decided to use is "list only those collections, where the remaining sum is less or equal to a given {amount}", which is implemented here. Sorry for any misunderstanding.***
