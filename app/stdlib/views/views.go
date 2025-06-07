package views

import (
	"fmt"
	"io"
	"os"
	"path/filepath"
	"text/template"
	"time"

	"github.com/labstack/echo/v4"
	"github.com/labstack/echo/v4/middleware"
)

const rootPath = "../resource/views/"

type Template struct {
	tmpl *template.Template
}

func New() (*Template, error) {
	var allTemplates []string

	t := &Template{tmpl: template.New("").Funcs(nil)}
	err := filepath.WalkDir(rootPath, func(path string, d os.DirEntry, err error) error {
		if err != nil {
			return err
		}

		if !d.IsDir() && filepath.Ext(path) == ".html" {
			allTemplates = append(allTemplates, path)
		}

		return nil
	})

	if err != nil {
		return nil, fmt.Errorf("error walking view directory: %w", err)
	}

	t.tmpl, err = t.tmpl.ParseFiles(allTemplates...)
	if err != nil {
		return nil, fmt.Errorf("error parsing templates: %w", err)
	}

	// Debug template ที่โหลด
	// for _, tmpl := range t.tmpl.Templates() {
	// 	fmt.Println("✅ Loaded template:", tmpl.Name())
	// }

	return t, nil
}

func (t *Template) Render(w io.Writer, name string, data interface{}, c echo.Context) error {
	var viewContext echo.Map
	if dataMap, ok := data.(echo.Map); ok {
		viewContext = dataMap
	} else {
		viewContext = echo.Map{}
	}

	viewContext["csrf"] = c.Get(middleware.DefaultCSRFConfig.ContextKey)
	viewContext["reverse"] = c.Echo().Reverse
	viewContext["time"] = time.Now().Unix()

	return t.tmpl.ExecuteTemplate(w, fmt.Sprintf("_pages/%s", name), viewContext)
}
