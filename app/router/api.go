package router

import "github.com/labstack/echo/v4/middleware"

func (s *server) api() {
	root := s.server

	root.Use(middleware.CORS())
}
