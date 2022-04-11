data "google_project" "project" {
}

resource "google_cloud_run_service" "contextual_help_api" {
  name     = "contextual-help-api"
  location = "europe-west1"

  autogenerate_revision_name = true

  template {
    spec {
      containers {
        image = "europe-west1-docker.pkg.dev/${local.gcp_project}/contextual-help-api/contextual-help-api:${var.app_version}"
        ports {
          name           = "http1"
          protocol       = "TCP"
          container_port = "80"
        }
        env {
          name = "GA_ACCOUNT_KEY"
          value_from {
            secret_key_ref {
              name = data.google_secret_manager_secret.contextual_help_api_google_analytics.secret_id
              key  = "latest"
            }
          }
        }
        env {
          name = "APP_KEY"
          value_from {
            secret_key_ref {
              name = data.google_secret_manager_secret.contextual_help_api_key.secret_id
              key  = "latest"
            }
          }
        }
        env {
          name  = "APP_ENV"
          value = "development"
        }
        env {
          name  = "APP_DEBUG"
          value = "false"
        }
        env {
          name  = "APACHE_RUN_USER"
          value = "apache-www-volume"
        }
        env {
          name  = "APACHE_RUN_GROUP"
          value = "apache-www-volume"
        }
        env {
          name = "Timestamp"
          value = timestamp()
        }
      }
    }
  }

    metadata {
      annotations = {
        "autoscaling.knative.dev/maxScale"      = "5"
        "autoscaling.knative.dev/minScale"      = "1"
        "run.googleapis.com/ingress"            = "all"
        "run.googleapis.com/ingress-status"     = "all"
      }
      labels = {
        "tribe"        = "core"
        "environement" = terraform.workspace
        "app"          = "contextual-help-api"  
        "commit"       = var.hash_id
      }
    }
  traffic {
    percent         = 100
    latest_revision = true
  }
  depends_on = [google_secret_manager_secret_iam_member.contextual_help_api_key, google_secret_manager_secret_iam_member.contextual_help_api_google_analytics]
}
