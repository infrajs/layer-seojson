# Свойство seojson для слоёв infrajs

Путь до json-файла с описанием SEO параметров страницы (title, keywords, description) указывает в описании слоя в свойстве 
```
{
  "tpl":"...",
  "div":"...",
  "seojson":"путь до файла с seo описанием"
}
```

При использовании мета тегов seo, для соц.сетей и т.п., прописываем код так 
```json
{
  "names":{
    "keywords":"test"
  },
  "properties":{
    "og:locale":"ru_RU"
  },
  "itemprops":{
    "description":"test"
  }
}
```
