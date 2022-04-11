data "google_iam_policy" "contextual_help_api" {
  binding {
    role = "roles/run.invoker"
    members = [
      "allUsers",
    ]
  }
}

resource "google_cloud_run_domain_mapping" "contextual_help_api" {
  location = "europe-west1"
  name     = local.mapped_domain

  metadata {
    namespace = local.gcp_project
  }

  spec {
    route_name       = google_cloud_run_service.contextual_help_api.name
  }
}
