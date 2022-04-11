resource "google_cloud_run_service_iam_policy" "contextual_help_api" {
  location    = google_cloud_run_service.contextual_help_api.location
  project     = google_cloud_run_service.contextual_help_api.project
  service     = google_cloud_run_service.contextual_help_api.name

  policy_data = data.google_iam_policy.contextual_help_api.policy_data
}

resource "google_artifact_registry_repository" "contextual_help_api" {
  provider = google-beta

  location      = "europe-west1"
  repository_id = "contextual-help-api"
  description   = "Contextual Help API docker repository"
  format        = "DOCKER"
  labels        = {
    "tribe"        = "core"
    "environement" = terraform.workspace
    "app"          = "contextual-help-api"
  }
}
