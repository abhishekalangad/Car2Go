# CAR2GO - PYTHON MIGRATION GUIDE

## 🎯 Overview

This guide provides a complete roadmap for migrating the CAR2GO PHP application to Python using **Django Framework**.

**Why Django?**
- Built-in admin panel (saves development time)
- ORM for database management
- Strong security features
- Authentication system
- Form validation
- File upload handling
- Template engine
- REST framework available

---

## 🏗️ Project Structure (Django)

```
car2go/
├── manage.py
├── requirements.txt
├── .env
├── .gitignore
├── README.md
│
├── config/                      # Project settings
│   ├── __init__.py
│   ├── settings.py
│   ├── urls.py
│   ├── wsgi.py
│   └── asgi.py
│
├── apps/
│   ├── accounts/                # User authentication
│   │   ├── migrations/
│   │   ├── __init__.py
│   │   ├── models.py           # User, Profile models
│   │   ├── forms.py            # Registration, Login forms
│   │   ├── views.py            # Auth views
│   │   ├── urls.py
│   │   └── admin.py
│   │
│   ├── cars/                    # Car rental module
│   │   ├── migrations/
│   │   ├── __init__.py
│   │   ├── models.py           # Car, Booking models
│   │   ├── forms.py
│   │   ├── views.py
│   │   ├── urls.py
│   │   └── admin.py
│   │
│   ├── drivers/                 # Driver module
│   │   ├── migrations/
│   │   ├── __init__.py
│   │   ├── models.py           # Driver, DriverBooking models
│   │   ├── forms.py
│   │   ├── views.py
│   │   ├── urls.py
│   │   └── admin.py
│   │
│   ├── services/                # Service center module
│   │   ├── migrations/
│   │   ├── __init__.py
│   │   ├── models.py           # ServiceCenter, Service, Booking
│   │   ├── forms.py
│   │   ├── views.py
│   │   ├── urls.py
│   │   └── admin.py
│   │
│   └── ratings/                 # Rating/Review module
│       ├── migrations/
│       ├── __init__.py
│       ├── models.py           # Rating, Review models
│       ├── forms.py
│       ├── views.py
│       ├── urls.py
│       └── admin.py
│
├── templates/                   # HTML templates
│   ├── base.html
│   ├── home.html
│   ├── accounts/
│   │   ├── login.html
│   │   ├── register.html
│   │   └── profile.html
│   ├── cars/
│   │   ├── list.html
│   │   ├── detail.html
│   │   └── booking.html
│   ├── drivers/
│   │   ├── list.html
│   │   └── detail.html
│   └── services/
│       ├── list.html
│       └── booking.html
│
├── static/                      # Static files
│   ├── css/
│   │   ├── bootstrap.css
│   │   └── style.css
│   ├── js/
│   │   ├── jquery.min.js
│   │   └── scripts.js
│   ├── fonts/
│   └── img/                     # Static images
│
└── media/                       # User uploads
    ├── profiles/
    ├── cars/
    ├── drivers/
    ├── services/
    └── documents/
```

---

## 📦 Requirements (requirements.txt)

```txt
# Core
Django==4.2.7
python-decouple==3.8        # For environment variables
Pillow==10.1.0              # Image handling

# Database
psycopg2-binary==2.9.9      # PostgreSQL (recommended)
# OR
mysqlclient==2.2.0          # MySQL

# Forms & Validation
django-crispy-forms==2.1    # Better form rendering
crispy-bootstrap4==2.0.0

# Security
django-environ==0.11.2
argon2-cffi==23.1.0         # Password hashing

# Location
geopy==2.4.0                # Geocoding

# Payments (optional)
stripe==7.4.0

# API (optional)
djangorestframework==3.14.0

# Development
django-debug-toolbar==4.2.0
black==23.11.0              # Code formatter
flake8==6.1.0               # Linting

# Production
gunicorn==21.2.0            # WSGI server
whitenoise==6.6.0           # Static file serving
django-storages==1.14.2     # Cloud storage
boto3==1.29.7               # AWS S3
```

---

## 🗄️ Database Models

### accounts/models.py

```python
from django.contrib.auth.models import AbstractUser
from django.db import models
from django.core.validators import RegexValidator

class User(AbstractUser):
    """Extended User model with role-based access"""
    
    USER_TYPE_CHOICES = [
        ('admin', 'Admin'),
        ('user', 'User'),
        ('driver', 'Driver'),
        ('service_center', 'Service Center'),
        ('employee', 'Employee'),
    ]
    
    user_type = models.CharField(max_length=20, choices=USER_TYPE_CHOICES)
    phone_regex = RegexValidator(
        regex=r'^\d{10}$', 
        message="Phone number must be 10 digits"
    )
    phone = models.CharField(validators=[phone_regex], max_length=10, blank=True)
    is_approved = models.BooleanField(default=True)
    
    class Meta:
        db_table = 'users'
        
    def __str__(self):
        return f"{self.username} ({self.user_type})"


class UserProfile(models.Model):
    """Profile for regular users"""
    user = models.OneToOneField(User, on_delete=models.CASCADE, related_name='user_profile')
    address = models.TextField()
    pincode = models.CharField(max_length=6, validators=[
        RegexValidator(r'^\d{6}$', 'Pincode must be 6 digits')
    ])
    id_proof = models.ImageField(upload_to='profiles/users/')
    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)
    
    class Meta:
        db_table = 'user_profiles'
        
    def __str__(self):
        return f"Profile: {self.user.username}"


class DriverProfile(models.Model):
    """Profile for drivers"""
    user = models.OneToOneField(User, on_delete=models.CASCADE, related_name='driver_profile')
    address = models.TextField()
    pincode = models.CharField(max_length=6)
    license_document = models.ImageField(upload_to='drivers/licenses/')
    id_proof = models.ImageField(upload_to='drivers/ids/')
    daily_charge = models.DecimalField(max_digits=10, decimal_places=2)
    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)
    
    class Meta:
        db_table = 'driver_profiles'
        
    def __str__(self):
        return f"Driver: {self.user.username}"
    
    def average_rating(self):
        from apps.ratings.models import DriverRating
        ratings = DriverRating.objects.filter(driver=self)
        if ratings.exists():
            return ratings.aggregate(models.Avg('rating'))['rating__avg']
        return 0


class ServiceCenterProfile(models.Model):
    """Profile for service centers"""
    user = models.OneToOneField(User, on_delete=models.CASCADE, related_name='service_profile')
    name = models.CharField(max_length=200)
    address = models.TextField()
    pincode = models.CharField(max_length=6)
    license_document = models.ImageField(upload_to='services/licenses/')
    rc_book = models.ImageField(upload_to='services/rc/')
    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)
    
    class Meta:
        db_table = 'service_center_profiles'
        
    def __str__(self):
        return self.name
```

### cars/models.py

```python
from django.db import models
from django.core.validators import MinValueValidator
from apps.accounts.models import User

class Car(models.Model):
    """Car listing model"""
    
    STATUS_CHOICES = [
        ('pending', 'Pending Approval'),
        ('approved', 'Approved'),
        ('rejected', 'Rejected'),
    ]
    
    owner = models.ForeignKey(User, on_delete=models.CASCADE, related_name='cars')
    company = models.CharField(max_length=100)
    model_name = models.CharField(max_length=100)
    year = models.IntegerField(validators=[MinValueValidator(1900)])
    registration_number = models.CharField(max_length=50, unique=True)
    seats = models.IntegerField(validators=[MinValueValidator(1)])
    
    # Rental details
    rent_per_km = models.DecimalField(max_digits=10, decimal_places=2)
    rent_per_day = models.DecimalField(max_digits=10, decimal_places=2)
    
    # Location
    pincode = models.CharField(max_length=6)
    
    # Additional info
    additional_info = models.TextField(blank=True)
    current_status = models.TextField(blank=True)
    accident_history = models.TextField(blank=True)
    
    # Documents
    car_photo = models.ImageField(upload_to='cars/photos/')
    rc_book = models.ImageField(upload_to='cars/rc/')
    insurance = models.ImageField(upload_to='cars/insurance/')
    pollution_certificate = models.ImageField(upload_to='cars/pollution/')
    
    # Approval
    status = models.CharField(max_length=20, choices=STATUS_CHOICES, default='pending')
    
    # Timestamps
    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)
    
    class Meta:
        db_table = 'cars'
        ordering = ['-created_at']
        
    def __str__(self):
        return f"{self.company} {self.model_name} ({self.year})"
    
    def average_rating(self):
        from apps.ratings.models import CarRating
        ratings = CarRating.objects.filter(car=self)
        if ratings.exists():
            return ratings.aggregate(models.Avg('rating'))['rating__avg']
        return 0


class CarBooking(models.Model):
    """Car rental booking"""
    
    STATUS_CHOICES = [
        ('pending', 'Pending'),
        ('confirmed', 'Confirmed'),
        ('completed', 'Completed'),
        ('cancelled', 'Cancelled'),
    ]
    
    car = models.ForeignKey(Car, on_delete=models.CASCADE, related_name='bookings')
    user = models.ForeignKey(User, on_delete=models.CASCADE, related_name='car_bookings')
    start_date = models.DateField()
    end_date = models.DateField()
    status = models.CharField(max_length=20, choices=STATUS_CHOICES, default='pending')
    payment_amount = models.DecimalField(max_digits=10, decimal_places=2, null=True, blank=True)
    
    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)
    
    class Meta:
        db_table = 'car_bookings'
        ordering = ['-created_at']
        
    def __str__(self):
        return f"Booking: {self.car} by {self.user.username}"
    
    def calculate_days(self):
        return (self.end_date - self.start_date).days
    
    def calculate_payment(self):
        days = self.calculate_days()
        return days * self.car.rent_per_day
```

### drivers/models.py

```python
from django.db import models
from apps.accounts.models import User, DriverProfile

class DriverBooking(models.Model):
    """Driver booking model"""
    
    STATUS_CHOICES = [
        ('pending', 'Pending'),
        ('confirmed', 'Confirmed'),
        ('completed', 'Completed'),
        ('cancelled', 'Cancelled'),
    ]
    
    driver = models.ForeignKey(DriverProfile, on_delete=models.CASCADE, related_name='bookings')
    user = models.ForeignKey(User, on_delete=models.CASCADE, related_name='driver_bookings')
    start_date = models.DateField()
    end_date = models.DateField()
    status = models.CharField(max_length=20, choices=STATUS_CHOICES, default='pending')
    payment_amount = models.DecimalField(max_digits=10, decimal_places=2, null=True, blank=True)
    
    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)
    
    class Meta:
        db_table = 'driver_bookings'
        ordering = ['-created_at']
        
    def __str__(self):
        return f"Driver Booking: {self.driver.user.username} by {self.user.username}"
    
    def calculate_days(self):
        return (self.end_date - self.start_date).days
    
    def calculate_payment(self):
        days = self.calculate_days()
        return days * self.driver.daily_charge
```

### services/models.py

```python
from django.db import models
from apps.accounts.models import User, ServiceCenterProfile
from apps.cars.models import Car

class Service(models.Model):
    """Service offered by service center"""
    service_center = models.ForeignKey(
        ServiceCenterProfile, 
        on_delete=models.CASCADE, 
        related_name='services'
    )
    name = models.CharField(max_length=200)
    description = models.TextField()
    price = models.DecimalField(max_digits=10, decimal_places=2)
    
    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)
    
    class Meta:
        db_table = 'services'
        
    def __str__(self):
        return f"{self.name} - {self.service_center.name}"


class ServiceBooking(models.Model):
    """General service booking"""
    
    STATUS_CHOICES = [
        ('pending', 'Pending'),
        ('approved', 'Approved'),
        ('rejected', 'Rejected'),
        ('completed', 'Completed'),
    ]
    
    service = models.ForeignKey(Service, on_delete=models.CASCADE, related_name='bookings')
    user = models.ForeignKey(User, on_delete=models.CASCADE, related_name='service_bookings')
    booking_date = models.DateField()
    status = models.CharField(max_length=20, choices=STATUS_CHOICES, default='pending')
    
    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)
    
    class Meta:
        db_table = 'service_bookings'
        ordering = ['-created_at']
        
    def __str__(self):
        return f"{self.service.name} booking by {self.user.username}"


class SpecializedServiceRequest(models.Model):
    """Custom service request with issue description"""
    
    user = models.ForeignKey(User, on_delete=models.CASCADE, related_name='special_requests')
    car = models.ForeignKey(Car, on_delete=models.CASCADE, related_name='service_requests')
    issue_description = models.TextField()
    request_date = models.DateField(auto_now_add=True)
    
    # Employee response
    response_date = models.DateField(null=True, blank=True)
    response = models.TextField(blank=True)
    responded_by = models.ForeignKey(
        User, 
        on_delete=models.SET_NULL, 
        null=True, 
        blank=True,
        related_name='responded_requests'
    )
    
    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)
    
    class Meta:
        db_table = 'specialized_service_requests'
        ordering = ['-created_at']
        
    def __str__(self):
        return f"Service Request for {self.car} by {self.user.username}"
```

### ratings/models.py

```python
from django.db import models
from django.core.validators import MinValueValidator, MaxValueValidator
from apps.accounts.models import User, DriverProfile, ServiceCenterProfile
from apps.cars.models import Car

class CarRating(models.Model):
    """Rating for car rental"""
    car = models.ForeignKey(Car, on_delete=models.CASCADE, related_name='ratings')
    user = models.ForeignKey(User, on_delete=models.CASCADE, related_name='car_ratings')
    rating = models.IntegerField(validators=[MinValueValidator(1), MaxValueValidator(5)])
    review = models.TextField(blank=True)
    
    created_at = models.DateTimeField(auto_now_add=True)
    
    class Meta:
        db_table = 'car_ratings'
        unique_together = ['car', 'user']
        ordering = ['-created_at']
        
    def __str__(self):
        return f"{self.rating}★ for {self.car}"


class DriverRating(models.Model):
    """Rating for driver"""
    driver = models.ForeignKey(DriverProfile, on_delete=models.CASCADE, related_name='ratings')
    user = models.ForeignKey(User, on_delete=models.CASCADE, related_name='driver_ratings')
    rating = models.IntegerField(validators=[MinValueValidator(1), MaxValueValidator(5)])
    review = models.TextField(blank=True)
    
    created_at = models.DateTimeField(auto_now_add=True)
    
    class Meta:
        db_table = 'driver_ratings'
        unique_together = ['driver', 'user']
        ordering = ['-created_at']
        
    def __str__(self):
        return f"{self.rating}★ for {self.driver}"


class ServiceCenterRating(models.Model):
    """Rating for service center"""
    service_center = models.ForeignKey(
        ServiceCenterProfile, 
        on_delete=models.CASCADE, 
        related_name='ratings'
    )
    user = models.ForeignKey(User, on_delete=models.CASCADE, related_name='service_ratings')
    rating = models.IntegerField(validators=[MinValueValidator(1), MaxValueValidator(5)])
    review = models.TextField(blank=True)
    
    created_at = models.DateTimeField(auto_now_add=True)
    
    class Meta:
        db_table = 'service_center_ratings'
        unique_together = ['service_center', 'user']
        ordering = ['-created_at']
        
    def __str__(self):
        return f"{self.rating}★ for {self.service_center}"
```

---

## 🔒 Authentication & Security

### accounts/views.py

```python
from django.contrib.auth import login, authenticate, logout
from django.contrib.auth.decorators import login_required
from django.shortcuts import render, redirect
from django.contrib import messages
from django.contrib.auth.hashers import make_password
from .forms import UserRegistrationForm, DriverRegistrationForm, ServiceCenterRegistrationForm
from .models import User, UserProfile, DriverProfile, ServiceCenterProfile

def register_user(request):
    """User registration view"""
    if request.method == 'POST':
        form = UserRegistrationForm(request.POST, request.FILES)
        if form.is_valid():
            # Create user
            user = form.save(commit=False)
            user.user_type = 'user'
            user.set_password(form.cleaned_data['password'])  # Hash password
            user.save()
            
            # Create profile
            UserProfile.objects.create(
                user=user,
                address=form.cleaned_data['address'],
                pincode=form.cleaned_data['pincode'],
                id_proof=form.cleaned_data['id_proof']
            )
            
            messages.success(request, 'Registration successful! Please login.')
            return redirect('login')
    else:
        form = UserRegistrationForm()
    
    return render(request, 'accounts/register_user.html', {'form': form})


def register_driver(request):
    """Driver registration view"""
    if request.method == 'POST':
        form = DriverRegistrationForm(request.POST, request.FILES)
        if form.is_valid():
            user = form.save(commit=False)
            user.user_type = 'driver'
            user.set_password(form.cleaned_data['password'])
            user.save()
            
            DriverProfile.objects.create(
                user=user,
                address=form.cleaned_data['address'],
                pincode=form.cleaned_data['pincode'],
                license_document=form.cleaned_data['license_document'],
                id_proof=form.cleaned_data['id_proof'],
                daily_charge=form.cleaned_data['daily_charge']
            )
            
            messages.success(request, 'Driver registration successful!')
            return redirect('login')
    else:
        form = DriverRegistrationForm()
    
    return render(request, 'accounts/register_driver.html', {'form': form})


def user_login(request):
    """Login view with role-based redirection"""
    if request.method == 'POST':
        username = request.POST.get('username')
        password = request.POST.get('password')
        
        user = authenticate(request, username=username, password=password)
        
        if user is not None:
            if user.is_approved:
                login(request, user)
                
                # Redirect based on user type
                if user.user_type == 'admin':
                    return redirect('admin:index')
                elif user.user_type == 'user':
                    return redirect('user_dashboard')
                elif user.user_type == 'driver':
                    return redirect('driver_dashboard')
                elif user.user_type == 'service_center':
                    return redirect('service_dashboard')
                elif user.user_type == 'employee':
                    return redirect('employee_dashboard')
            else:
                messages.error(request, 'Your account is pending approval.')
        else:
            messages.error(request, 'Invalid username or password.')
    
    return render(request, 'accounts/login.html')


@login_required
def user_logout(request):
    """Logout view"""
    logout(request)
    messages.success(request, 'Logged out successfully.')
    return redirect('home')
```

### accounts/forms.py

```python
from django import forms
from django.contrib.auth.forms import UserCreationForm
from .models import User

class UserRegistrationForm(forms.ModelForm):
    password = forms.CharField(
        widget=forms.PasswordInput,
        min_length=8,
        help_text="Password must be at least 8 characters"
    )
    confirm_password = forms.CharField(widget=forms.PasswordInput)
    address = forms.CharField(widget=forms.Textarea(attrs={'rows': 3}))
    pincode = forms.CharField(max_length=6, min_length=6)
    id_proof = forms.ImageField()
    
    class Meta:
        model = User
        fields = ['username', 'email', 'first_name', 'last_name', 'phone']
    
    def clean(self):
        cleaned_data = super().clean()
        password = cleaned_data.get('password')
        confirm_password = cleaned_data.get('confirm_password')
        
        if password != confirm_password:
            raise forms.ValidationError("Passwords do not match")
        
        return cleaned_data


class DriverRegistrationForm(forms.ModelForm):
    password = forms.CharField(widget=forms.PasswordInput, min_length=8)
    confirm_password = forms.CharField(widget=forms.PasswordInput)
    address = forms.CharField(widget=forms.Textarea(attrs={'rows': 3}))
    pincode = forms.CharField(max_length=6, min_length=6)
    license_document = forms.ImageField()
    id_proof = forms.ImageField()
    daily_charge = forms.DecimalField(max_digits=10, decimal_places=2)
    
    class Meta:
        model = User
        fields = ['username', 'email', 'first_name', 'last_name', 'phone']
    
    def clean(self):
        cleaned_data = super().clean()
        password = cleaned_data.get('password')
        confirm_password = cleaned_data.get('confirm_password')
        
        if password != confirm_password:
            raise forms.ValidationError("Passwords do not match")
        
        return cleaned_data
```

---

## 🔍 Search Functionality (Location-Based)

### cars/views.py

```python
from django.shortcuts import render
from django.contrib.auth.decorators import login_required
from django.db.models import Q, Avg
from .models import Car
from apps.accounts.models import UserProfile

@login_required
def search_cars(request):
    """Search cars by location (pincode proximity)"""
    user_profile = UserProfile.objects.get(user=request.user)
    user_pincode = int(user_profile.pincode)
    
    # Default radius: 10km (adjust pincode range accordingly)
    radius = int(request.GET.get('radius', 10))
    
    # Calculate pincode range (approximate)
    min_pincode = user_pincode - radius
    max_pincode = user_pincode + radius
    
    # Filter approved cars within pincode range
    cars = Car.objects.filter(
        status='approved',
        pincode__gte=str(min_pincode),
        pincode__lte=str(max_pincode)
    ).annotate(
        avg_rating=Avg('ratings__rating')
    ).order_by('-avg_rating')
    
    context = {
        'cars': cars,
        'radius': radius,
        'user_pincode': user_pincode
    }
    
    return render(request, 'cars/search.html', context)


@login_required
def car_detail(request, car_id):
    """View car details"""
    car = Car.objects.get(id=car_id)
    ratings = car.ratings.all().order_by('-created_at')[:10]
    
    context = {
        'car': car,
        'ratings': ratings,
        'average_rating': car.average_rating()
    }
    
    return render(request, 'cars/detail.html', context)
```

---

## 📝 Booking System

### cars/views.py (continued)

```python
from django.shortcuts import get_object_or_404
from .models import CarBooking
from .forms import CarBookingForm

@login_required
def book_car(request, car_id):
    """Book a car"""
    car = get_object_or_404(Car, id=car_id, status='approved')
    
    if request.method == 'POST':
        form = CarBookingForm(request.POST)
        if form.is_valid():
            booking = form.save(commit=False)
            booking.car = car
            booking.user = request.user
            booking.status = 'pending'
            booking.save()
            
            messages.success(request, 'Booking request sent! Waiting for owner confirmation.')
            return redirect('user_dashboard')
    else:
        form = CarBookingForm()
    
    context = {
        'car': car,
        'form': form
    }
    
    return render(request, 'cars/book.html', context)


@login_required
def my_car_bookings(request):
    """View bookings for cars owned by user"""
    my_cars = Car.objects.filter(owner=request.user)
    bookings = CarBooking.objects.filter(car__in=my_cars).order_by('-created_at')
    
    return render(request, 'cars/my_bookings.html', {'bookings': bookings})


@login_required
def confirm_booking(request, booking_id):
    """Confirm a car booking (car owner only)"""
    booking = get_object_or_404(CarBooking, id=booking_id, car__owner=request.user)
    
    if request.method == 'POST':
        booking.status = 'confirmed'
        booking.payment_amount = booking.calculate_payment()
        booking.save()
        
        messages.success(request, 'Booking confirmed!')
        return redirect('my_car_bookings')
    
    return render(request, 'cars/confirm_booking.html', {'booking': booking})
```

---

## 🛡️ Admin Panel Configuration

### cars/admin.py

```python
from django.contrib import admin
from .models import Car, CarBooking

@admin.register(Car)
class CarAdmin(admin.ModelAdmin):
    list_display = ['company', 'model_name', 'owner', 'status', 'rent_per_day', 'created_at']
    list_filter = ['status', 'company', 'created_at']
    search_fields = ['company', 'model_name', 'registration_number', 'owner__username']
    actions = ['approve_cars', 'reject_cars']
    
    def approve_cars(self, request, queryset):
        queryset.update(status='approved')
        self.message_user(request, f"{queryset.count()} cars approved.")
    
    def reject_cars(self, request, queryset):
        queryset.update(status='rejected')
        self.message_user(request, f"{queryset.count()} cars rejected.")


@admin.register(CarBooking)
class CarBookingAdmin(admin.ModelAdmin):
    list_display = ['car', 'user', 'start_date', 'end_date', 'status', 'payment_amount']
    list_filter = ['status', 'start_date']
    search_fields = ['car__company', 'user__username']
```

---

## 🚀 Settings Configuration

### config/settings.py

```python
import os
from pathlib import Path
from decouple import config

BASE_DIR = Path(__file__).resolve().parent.parent

# Security
SECRET_KEY = config('SECRET_KEY')
DEBUG = config('DEBUG', default=False, cast=bool)
ALLOWED_HOSTS = config('ALLOWED_HOSTS', default='').split(',')

# Applications
INSTALLED_APPS = [
    'django.contrib.admin',
    'django.contrib.auth',
    'django.contrib.contenttypes',
    'django.contrib.sessions',
    'django.contrib.messages',
    'django.contrib.staticfiles',
    
    # Third party
    'crispy_forms',
    'crispy_bootstrap4',
    
    # Local apps
    'apps.accounts',
    'apps.cars',
    'apps.drivers',
    'apps.services',
    'apps.ratings',
]

MIDDLEWARE = [
    'django.middleware.security.SecurityMiddleware',
    'whitenoise.middleware.WhiteNoiseMiddleware',  # Static files
    'django.contrib.sessions.middleware.SessionMiddleware',
    'django.middleware.common.CommonMiddleware',
    'django.middleware.csrf.CsrfViewMiddleware',
    'django.contrib.auth.middleware.AuthenticationMiddleware',
    'django.contrib.messages.middleware.MessageMiddleware',
    'django.middleware.clickjacking.XFrameOptionsMiddleware',
]

# Database
DATABASES = {
    'default': {
        'ENGINE': 'django.db.backends.postgresql',
        'NAME': config('DB_NAME'),
        'USER': config('DB_USER'),
        'PASSWORD': config('DB_PASSWORD'),
        'HOST': config('DB_HOST', default='localhost'),
        'PORT': config('DB_PORT', default='5432'),
    }
}

# Custom User Model
AUTH_USER_MODEL = 'accounts.User'

# Password Validation
AUTH_PASSWORD_VALIDATORS = [
    {'NAME': 'django.contrib.auth.password_validation.UserAttributeSimilarityValidator'},
    {'NAME': 'django.contrib.auth.password_validation.MinimumLengthValidator'},
    {'NAME': 'django.contrib.auth.password_validation.CommonPasswordValidator'},
    {'NAME': 'django.contrib.auth.password_validation.NumericPasswordValidator'},
]

# Password Hashing
PASSWORD_HASHERS = [
    'django.contrib.auth.hashers.Argon2PasswordHasher',
    'django.contrib.auth.hashers.PBKDF2PasswordHasher',
    'django.contrib.auth.hashers.PBKDF2SHA1PasswordHasher',
    'django.contrib.auth.hashers.BCryptSHA256PasswordHasher',
]

# Static files
STATIC_URL = '/static/'
STATIC_ROOT = BASE_DIR / 'staticfiles'
STATICFILES_DIRS = [BASE_DIR / 'static']
STATICFILES_STORAGE = 'whitenoise.storage.CompressedManifestStaticFilesStorage'

# Media files
MEDIA_URL = '/media/'
MEDIA_ROOT = BASE_DIR / 'media'

# File Upload
FILE_UPLOAD_MAX_MEMORY_SIZE = 5242880  # 5MB
DATA_UPLOAD_MAX_MEMORY_SIZE = 5242880

# Security Settings
SECURE_BROWSER_XSS_FILTER = True
SECURE_CONTENT_TYPE_NOSNIFF = True
X_FRAME_OPTIONS = 'DENY'
CSRF_COOKIE_SECURE = not DEBUG
SESSION_COOKIE_SECURE = not DEBUG
SECURE_SSL_REDIRECT = not DEBUG

# Session
SESSION_COOKIE_AGE = 3600  # 1 hour
SESSION_SAVE_EVERY_REQUEST = True

# Login URLs
LOGIN_URL = '/accounts/login/'
LOGIN_REDIRECT_URL = '/'
LOGOUT_REDIRECT_URL = '/'

# Email (for notifications)
EMAIL_BACKEND = 'django.core.mail.backends.smtp.EmailBackend'
EMAIL_HOST = config('EMAIL_HOST', default='smtp.gmail.com')
EMAIL_PORT = config('EMAIL_PORT', default=587, cast=int)
EMAIL_USE_TLS = True
EMAIL_HOST_USER = config('EMAIL_HOST_USER', default='')
EMAIL_HOST_PASSWORD = config('EMAIL_HOST_PASSWORD', default='')
DEFAULT_FROM_EMAIL = config('DEFAULT_FROM_EMAIL', default='noreply@car2go.com')
```

### .env (Environment Variables)

```env
# Django
SECRET_KEY=your-secret-key-here-change-in-production
DEBUG=True
ALLOWED_HOSTS=localhost,127.0.0.1

# Database
DB_NAME=car2go
DB_USER=postgres
DB_PASSWORD=yourpassword
DB_HOST=localhost
DB_PORT=5432

# Email
EMAIL_HOST=smtp.gmail.com
EMAIL_PORT=587
EMAIL_HOST_USER=your-email@gmail.com
EMAIL_HOST_PASSWORD=your-app-password
DEFAULT_FROM_EMAIL=noreply@car2go.com

# AWS S3 (Optional, for production)
USE_S3=False
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_STORAGE_BUCKET_NAME=
AWS_S3_REGION_NAME=us-east-1
```

---

## 📋 Migration Commands

```bash
# Initial setup
python manage.py makemigrations
python manage.py migrate

# Create superuser
python manage.py createsuperuser

# Load initial data (optional)
python manage.py loaddata initial_data.json

# Run development server
python manage.py runserver
```

---

## ✅ Checklist for Migration

- [ ] Set up Django project structure
- [ ] Create all models with proper relationships
- [ ] Implement user authentication with password hashing
- [ ] Add role-based access control
- [ ] Create registration forms with validation
- [ ] Implement location-based search
- [ ] Add booking system with status tracking
- [ ] Create rating/review system
- [ ] Configure admin panel for approvals
- [ ] Add file upload with validation
- [ ] Implement email notifications
- [ ] Add CSRF protection
- [ ] Secure file uploads
- [ ] Add input sanitization
- [ ] Implement session timeouts
- [ ] Create user dashboards
- [ ] Add pagination for lists
- [ ] Implement search filters
- [ ] Add payment gateway integration (optional)
- [ ] Create REST API (optional)
- [ ] Write unit tests
- [ ] Set up logging
- [ ] Configure production settings
- [ ] Deploy with Docker

---

## 🎨 Template Example

### templates/base.html

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{% block title %}CAR2GO{% endblock %}</title>
    
    {% load static %}
    <link rel="stylesheet" href="{% static 'css/bootstrap.css' %}">
    <link rel="stylesheet" href="{% static 'css/style.css' %}">
    
    {% block extra_css %}{% endblock %}
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{% url 'home' %}">CAR2GO</a>
            
            <div class="navbar-nav ml-auto">
                {% if user.is_authenticated %}
                    <span class="navbar-text">Welcome, {{ user.username }}</span>
                    <a class="nav-link" href="{% url 'logout' %}">Logout</a>
                {% else %}
                    <a class="nav-link" href="{% url 'login' %}">Login</a>
                    <a class="nav-link" href="{% url 'register' %}">Register</a>
                {% endif %}
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        {% if messages %}
            {% for message in messages %}
                <div class="alert alert-{{ message.tags }}">
                    {{ message }}
                </div>
            {% endfor %}
        {% endif %}
        
        {% block content %}{% endblock %}
    </div>
    
    <script src="{% static 'js/jquery.min.js' %}"></script>
    <script src="{% static 'js/bootstrap.js' %}"></script>
    {% block extra_js %}{% endblock %}
</body>
</html>
```

---

## 🧪 Testing

### tests/test_models.py

```python
from django.test import TestCase
from apps.accounts.models import User, UserProfile
from apps.cars.models import Car

class CarModelTest(TestCase):
    def setUp(self):
        self.user = User.objects.create_user(
            username='testuser',
            email='test@test.com',
            password='testpass123',
            user_type='user'
        )
        
    def test_create_car(self):
        car = Car.objects.create(
            owner=self.user,
            company='Tesla',
            model_name='Model 3',
            year=2023,
            registration_number='KL-01-1234',
            seats=5,
            rent_per_km=10.00,
            rent_per_day=1000.00,
            pincode='680001'
        )
        self.assertEqual(str(car), 'Tesla Model 3 (2023)')
        self.assertEqual(car.status, 'pending')
```

---

## 📦 Deployment (Production)

### Dockerfile

```dockerfile
FROM python:3.11-slim

ENV PYTHONUNBUFFERED=1
WORKDIR /app

RUN apt-get update && apt-get install -y \
    postgresql-client \
    && rm -rf /var/lib/apt/lists/*

COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

COPY . .

RUN python manage.py collectstatic --noinput

EXPOSE 8000

CMD ["gunicorn", "config.wsgi:application", "--bind", "0.0.0.0:8000"]
```

### docker-compose.yml

```yaml
version: '3.8'

services:
  db:
    image: postgres:15
    environment:
      POSTGRES_DB: car2go
      POSTGRES_USER: postgres
      POSTGRES_PASSWORD: yourpassword
    volumes:
      - postgres_data:/var/lib/postgresql/data

  web:
    build: .
    command: gunicorn config.wsgi:application --bind 0.0.0.0:8000
    volumes:
      - .:/app
      - static_volume:/app/staticfiles
      - media_volume:/app/media
    ports:
      - "8000:8000"
    depends_on:
      - db
    env_file:
      - .env

volumes:
  postgres_data:
  static_volume:
  media_volume:
```

---

## 📚 Next Steps

1. **Phase 1 - Core Setup** (Week 1-2)
   - Initialize Django project
   - Create all models
   - Set up authentication

2. **Phase 2 - Features** (Week 3-4)
   - Implement booking systems
   - Add search functionality
   - Create dashboards

3. **Phase 3 - Polish** (Week 5-6)
   - Add rating system
   - Implement notifications
   - Add admin approvals

4. **Phase 4 - Testing & Deploy** (Week 7-8)
   - Write tests
   - Security audit
   - Production deployment

---

**End of Migration Guide**

This guide provides a complete foundation for migrating CAR2GO from PHP to Python/Django with improved security, scalability, and maintainability.
